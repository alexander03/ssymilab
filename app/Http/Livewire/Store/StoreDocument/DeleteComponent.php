<?php

namespace App\Http\Livewire\Store\StoreDocument;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\Lot;
use App\Models\StoreDocument;
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
        return view('livewire.store.store-document.delete-component');
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $storeDocument = StoreDocument::find($this->model_id);

            $details = $storeDocument->details;

            foreach ($details as $detail) {
                $product_moves = DocumentDetail::product_moves($detail->id);
                foreach ($product_moves as $product_move) {
                    $product_move->update(['status' => 'A']);
                }
                if($storeDocument->dispatched=="Y" || $storeDocument->dispatched=="E"){//si esta despachado
                    if($store_document->document_type->type == "I"){
                        $this->decrease_stock($store_document, $detail);
                    }else{
                        $this->increase_stock($storeDocument, $detail);
                    }
                }
            }

            $storeDocument->update(["status" => "A"]);

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
