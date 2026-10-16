<?php

namespace App\Http\Livewire\Sale\Creditnote;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\CreditNote;
use App\Models\ProductMove;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

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
        return view('livewire.sale.creditnote.delete-component');
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $creditnote = CreditNote::find($this->model_id);
            if($creditnote->dispatched=="Y"){
                foreach ($this->details as $value) {
                    $detail = DocumentDetail::find($value["id"]);
                    if($detail){
                        $detail_status = "Y";
                        if($value["dispatched_quantity"] != $value["quantity"]){
                            $detail_status = "E";
                        }
                        $detail->update([
                            "dispatched_quantity" => $value["dispatched_quantity"],
                            "status" => $detail_status
                        ]);

                        foreach ($value["product_moves"] as $value2) {
                            $product_move = ProductMove::find($value2["id"]);
                            $detail_product_move = "Y";
                            if($value2["dispatched_quantity"] != $value2["quantity"]){
                                $detail_product_move = "E";
                            }
                            $product_move->update([
                                "dispatched_quantity" => $value2["dispatched_quantity"],
                                "status" => $detail_product_move
                            ]);
                        }
                        $this->decrease_stock2($creditnote, $detail);
                    }

                }

                if($creditnote){
                    $status = "Y";
                    foreach ($creditnote->details as $key => $value) {
                        if($value->status == "E"){
                            $status = "E";
                        }
                    }
                    $creditnote->update([
                        "dispatched" => $status,
                    ]);
                }
            }
            $creditnote->update(["status" => "A"]);


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
