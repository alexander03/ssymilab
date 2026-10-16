<?php

namespace App\Http\Livewire\Purchase\EntryGuide;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\Lot;
use App\Models\EntryGuide;
use App\Models\ProductMove;
use App\Models\ProductSerie;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeleteComponent extends Component
{
    use StockTrait;

    public $open = false;

    public $model_id = null;

    protected $listeners = [
        'toggle-modal' => 'toggle_modal'
    ];

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }

    }

    public function render()
    {
        return view('livewire.purchase.entry-guide.delete-component');
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $guide = EntryGuide::find($this->model_id);

            $purchase = $guide->document;

            $details = $guide->details;

            foreach ($details as $detail) {
                $product_moves_sale = DocumentDetail::product_moves_guide($detail->id,$purchase->id);//para los detalles de la venta
                foreach ($product_moves_sale as $product_move) {
                    $product_move->update(['status' => 'N',
                                            'guide_detail_id' => null,
                                            'dispatched_quantity' => $product_move->dispatched_quantity - $detail->dispatched_quantity,
                                            ]);
                    if($product_move->document_detail){
                        if($guide->dispatched=="Y"){//si esta despachado
                            $product_move->document_detail->update([
                                'dispatched_quantity' => $product_move->document_detail->dispatched_quantity - $detail->dispatched_quantity,
                                'status' => 'P',
                            ]);
                        }else{
                            $product_move->document_detail->update([
                                'dispatched_quantity' => $product_move->document_detail->dispatched_quantity - $detail->quantity,
                                'status' => 'P',
                            ]);
                        }
                    }
                }
                $product_moves = DocumentDetail::product_moves($detail->id);
                foreach ($product_moves as $product_move) {
                    $product_move->update(['status' => 'A']);
                }
                if($guide->dispatched=="Y" || $guide->dispatched=="E"){//si esta despachado
                    $this->decrease_stock($guide, $detail);
                }
            }

            $guide->update(["status" => "A"]);

            if($purchase){
                $purchase->update(["dispatched" => "N"]);
            }

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se anuló correctamente',
            ]);

        } catch (\Exception $th) {

            DB::rollback();

            $this->emit('alert-error', [
                'title' => '¡Error!',
                'text' => 'Algo salio mal: ' . $th->getMessage(),
            ]);
        }
    }
}
