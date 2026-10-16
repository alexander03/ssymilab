<?php

namespace App\Http\Livewire\Store\StoreDocument;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\StoreDocument;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\ProductMove;

class DispatchedComponent extends Component
{
    use StockTrait;

    public $open = false;

    public $model_id = null;

    public $detail_checkboxes = [];
    public $search_barcode;

    protected $listeners = [
        'toggle-modal-dispatched' => 'toggle_modal'
    ];

    public function render()
    {
        $details = null;
        $store_document = null;
        if($this->model_id != null){
            $store_document = StoreDocument::find($this->model_id);
            if($store_document){
                $details = $store_document->details;
                foreach ($details as $key => $value) {
                    if($value->status == "N"){
                        $this->detail_checkboxes[] = "" . $value->id;
                    }
                    $product_moves = DocumentDetail::product_moves_guide($value->id);
                    $details[$key]->product_moves = $product_moves;
                }
            }
        }
        return view('livewire.store.store-document.dispatched-component')->with(compact('details', 'store_document'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
            $this->detail_checkboxes = [];
            $this->search_barcode = null;
        }

    }

    public function check_barcode(){
        if($this->search_barcode && $this->search_barcode != ""){
            $store_document = StoreDocument::find($this->model_id);
            if($store_document){
                $details = $store_document->details;
                $exists = false;
                foreach ($details as $key => $value) {
                    if($value->product->barcode == $this->search_barcode){
                        if(!in_array("" . $value->id, $this->detail_checkboxes)){
                            $this->detail_checkboxes[] = "" . $value->id;
                        }
                        $exists = true;
                    }
                }
                if(!$exists){
                    $this->emit('alert-warning', [
                        'title' => 'Advertencia!',
                        'text' => 'No se encontró producto',
                    ]);
                }
            }
        }
    }

    public function save()
    {
        DB::beginTransaction();
        try {

            $store_document = StoreDocument::find($this->model_id);

            foreach ($this->detail_checkboxes as $key => $value) {
                $detail = DocumentDetail::find($value);
                if($detail){
                    $detail->update([
                        "dispatched_quantity" => $detail->quantity,
                        "status" => "Y"
                    ]);

                    foreach ($detail->product_moves_guide($detail->id) as $value2) {
                        $product_move = ProductMove::find($value2["id"]);
                        $detail_product_move = "Y";
                        $product_move->update([
                            "dispatched_quantity" => $value2["quantity"],
                            "status" => $detail_product_move
                        ]);
                    }
                }

                if($store_document->document_type->type == "I"){
                    $this->increase_stock($store_document, $detail);
                }else{
                    $this->decrease_stock($store_document, $detail);
                }

            }

            if($store_document){
                $status = "Y";
                foreach ($store_document->details as $key => $value) {
                    if($value->status == "N"){
                        $status = "N";
                    }
                }
                $store_document->update([
                    "dispatched" => $status,
                ]);
            }

            DB::commit();

            $this->emit('search-and-reset-pages');

            $this->open = false;

            $this->emit('alert-success', [
                'title' => '¡Exito!',
                'text' => 'Se registró correctamente',
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
