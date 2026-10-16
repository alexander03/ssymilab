<?php

namespace App\Http\Livewire\Store\ReferralGuide;

use App\Http\Traits\General\StockTrait;
use App\Models\DocumentDetail;
use App\Models\ProductMove;
use App\Models\ReferralGuide;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DispatchedComponent extends Component
{
    use StockTrait;

    public $open = false;

    public $model_id = null;

    public $detail_checkboxes = [];
    public $search_barcode;

    public $details;
    public $detail_key = "-1";
    public $lote_key = "-1";

    protected $listeners = [
        'toggle-modal-dispatched' => 'toggle_modal'
    ];

    public function render()
    {
        $details = null;
        $referral_guide = null;
        if($this->model_id != null){
            $referral_guide = ReferralGuide::find($this->model_id);
        }
        return view('livewire.store.referral-guide.dispatched-component')->with(compact('referral_guide'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
            $this->detail_checkboxes = [];
            $this->search_barcode = null;

            $referral_guide = ReferralGuide::find($this->model_id);
            if($referral_guide){
                $details = $referral_guide->details;
                foreach ($details as $key => $value) {
                    $product_moves = DocumentDetail::product_moves_guide($value["id"]);
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
    public function check_barcode(){
        if($this->search_barcode && $this->search_barcode != ""){
            if($this->detail_key != "-1" && $this->lote_key != "-1"){
                if($this->details[$this->detail_key]["product_moves"][$this->lote_key]["product"]["barcode"] == $this->search_barcode){
                    if($this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] < $this->details[$this->detail_key]["product_moves"][$this->lote_key]["quantity"]){
                        $this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] = $this->details[$this->detail_key]["product_moves"][$this->lote_key]["dispatched_quantity"] + 1;
                    }
                }
                $this->calculate_quantity();
            }else{
                foreach ($this->details as $key => $detail) {
                    foreach ($detail["product_moves"] as $key2 => $product_move) {
                        if($product_move["product_serie"]["serie"] == $this->search_barcode){
                            if($product_move["dispatched_quantity"] >= $product_move["quantity"]){
                                $this->emit('alert-warning', [
                                    'title' => 'Advertencia!',
                                    'text' => 'Ya se seleccionó la serie',
                                ]);
                            }else{
                                $this->details[$key]["product_moves"][$key2]["dispatched_quantity"] = $product_move["dispatched_quantity"] + 1;
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

            $referral_guide = ReferralGuide::find($this->model_id);

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
                    $this->decrease_stock($referral_guide, $detail);
                }

            }

            if($referral_guide){
                $status = "Y";
                foreach ($referral_guide->details as $key => $value) {
                    if($value->status == "E"){
                        $status = "E";
                    }
                }
                $referral_guide->update([
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
