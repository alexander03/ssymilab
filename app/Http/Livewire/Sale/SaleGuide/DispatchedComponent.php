<?php

namespace App\Http\Livewire\Sale\SaleGuide;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\ProductMove;
use App\Models\SaleGuide;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DispatchedComponent extends Component
{
    use StockTrait;

    public $open = false;

    public $model_id = null;

    public $detail_checkboxes = [];
    public $search_barcode,$product_lot,$product_barcode,$product_due_date;

    public $details;
    public $lot_quantity = 0;
    public $detail_key = "-1";
    public $lote_key = "-1";

    protected $listeners = [
        'toggle-modal-dispatched' => 'toggle_modal'
    ];

    public function render()
    {
        $details = null;
        $sale_guide = null;
        if($this->model_id != null){
            $sale_guide = SaleGuide::find($this->model_id);
        }
        return view('livewire.sale.sale-guide.dispatched-component')->with(compact('sale_guide'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
            $this->detail_checkboxes = [];
            $this->search_barcode = null;

            $sale_guide = SaleGuide::find($this->model_id);
            if($sale_guide){
                $details = $sale_guide->details;
                foreach ($details as $key => $value) {
                    $product_moves = DocumentDetail::product_moves_guide($value["id"],$this->model_id);
                    foreach ($product_moves as $key2 => $value2) {
                        $product_moves[$key2]->product;
                        $product_moves[$key2]->product_serie;
                        $product_moves[$key2]->lot;
                    }
                    $details[$key]["product_moves"] = $product_moves->toArray();
                }

                $this->details = $details->toArray();
                $this->detail_key = "-1";
                $this->lote_key = "-1";
            }
        }
    }

    public function add_product_lot(){
        
        if($this->lot_quantity > $this->details[$this->detail_key]["product_moves"][$this->lote_key]["quantity"]){
            $this->emit('alert-warning', [
                    'title' => 'Advertencia!',
                    'text' => 'Despacho incorrecto',
                ]);
        }else{
            $this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] = $this->lot_quantity;
            $this->product_barcode = "";
            $this->product_lot = "";
            $this->product_due_date = "";
            $this->lot_quantity = 0;
            $this->detail_key = "-1";
            $this->lote_key = "-1";
            $this->calculate_quantity();
        }
    }

    public function check_barcode(){
        if($this->search_barcode && $this->search_barcode != ""){
            if($this->detail_key != "-1" && $this->lote_key != "-1"){
                if($this->details[$this->detail_key]["product_moves"][$this->lote_key]["product"]["barcode"] == $this->search_barcode){
                    if($this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] < $this->details[$this->detail_key]["product_moves"][$this->lote_key]["quantity"]){
                        $this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] = $this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] + 1;
                    }
                }elseif($this->details[$this->detail_key]["product_moves"][$this->lote_key]["lot"]["barcode"]==$this->search_barcode){
                    if($this->product_lot==""){
                        $this->product_lot = $this->details[$this->detail_key]["product_moves"][$this->lote_key]["lot"]["lot"];
                        $this->product_barcode = $this->details[$this->detail_key]["product_moves"][$this->lote_key]["lot"]["barcode"];
                        $this->product_due_date = $this->details[$this->detail_key]["product_moves"][$this->lote_key]["lot"]["due_date"];
                        $this->lot_quantity = 1;
                    }else{
                        $this->lot_quantity = $this->lot_quantity + 1;
                    }
                }
                $this->calculate_quantity();
            }else{
                foreach ($this->details as $key => $detail) {
                    foreach ($detail["product_moves"] as $key2 => $product_move) {
                        if(!is_null($product_move["product_serie"]) && $product_move["product_serie"]["serie"] == $this->search_barcode){
                            if($product_move["dispatched_quantity"] >= $product_move["quantity"]){
                                $this->emit('alert-warning', [
                                    'title' => 'Advertencia!',
                                    'text' => 'Ya se seleccionó la serie',
                                ]);
                            }else{
                                $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] = $product_move["dispatched_quantity"] + 1;
                            }
                        }else{
                            if($product_move["lot"]["barcode"] == $this->search_barcode){
                                if($product_move["dispatched_quantity"] >= $product_move["quantity"]){
                                    $this->emit('alert-warning', [
                                        'title' => 'Advertencia!',
                                        'text' => 'Ya se seleccionó la serie',
                                    ]);
                                }else{
                                    if($this->product_lot==""){
                                        $this->detail_key = $key;
                                        $this->lote_key = $key2;
                                        $this->product_lot = $product_move["lot"]["lot"];
                                        $this->product_barcode = $product_move["lot"]["barcode"];
                                        $this->product_due_date = $product_move["lot"]["due_date"];
                                        $this->lot_quantity = 1;
                                    }else{
                                        $this->lot_quantity = $this->lot_quantity + 1;
                                    }
                                }
                            }
                        }
                    }
                }
                $this->calculate_quantity();
            }
            $this->search_barcode = "";
        }
    }

    private function calculate_quantity(){
        foreach ($this->details as $key => $detail) {
            $cantidad = 0;
            foreach ($detail["product_moves"] as $key2 => $product_move) {
                $cantidad = $cantidad + $product_move["dispatched_quantity"];
            }
            $this->details[$key]["dispatched_quantity"] = $cantidad;
        }
    }

    public function add_product_move($key, $key2){
        if($this->details[$key]["product_moves"][$key2]["dispatched_quantity"] < $this->details[$key]["product_moves"][$key2]["quantity"]){
            $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] = $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] + 1;
            $this->calculate_quantity();
        }
    }

    public function add_product_move_all($key, $key2){
        if($this->details[$key]["product_moves"][$key2]["dispatched_quantity"] < $this->details[$key]["product_moves"][$key2]["quantity"]){
            $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] = $this->details[$key]["product_moves"][$key2]["quantity"];
            $this->calculate_quantity();
        }
    }

    public function quit_product_move($key, $key2){
        if($this->details[$key]["product_moves"][$key2]["dispatched_quantity"] > 0){
            $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] = $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] - 1;
            $this->calculate_quantity();
        }
    }

    public function select_lot($key, $key2){
        $this->detail_key = "" . $key;
        $this->lote_key = "" . $key2;
    }

    public function unselect_lot(){
        $this->detail_key = "-1";
        $this->lote_key = "-1";
    }

    public function save()
    {
        DB::beginTransaction();
        try {

            $sale_guide = SaleGuide::find($this->model_id);

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
                    $this->decrease_stock($sale_guide, $detail);
                }

            }

            if($sale_guide){
                $status = "Y";
                foreach ($sale_guide->details as $key => $value) {
                    if($value->status == "E"){
                        $status = "E";
                    }
                }
                $sale_guide->update([
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
