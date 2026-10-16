<?php

namespace App\Http\Livewire\Purchase\Purchase;

use App\Models\Card;
use App\Models\DocumentPayment;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\TransferMethod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PaymentComponent extends Component
{

    public $open = false;

    public $model_id = null;

    public $current_payment_id = null;
    public $current_payment_name = "";
    public $current_payment_amount = "";
    public $current_payment_symbol = "";

    public $payment_methods;
    public $payments;

    protected $listeners = [
        'toggle-modal-payment' => 'toggle_modal'
    ];

    public function mount(){
        $this->payment_methods[] = [
            "type" => "E",
            "name" => "EFECTIVO",
        ];

        $cards = Card::all();
        foreach ($cards as $key => $card) {
            $this->payment_methods[] = [
                "type" => "T",
                "name" => $card->name,
            ];
        }
        $transfers = TransferMethod::all();
        foreach ($transfers as $key => $transfer) {
            $this->payment_methods[] = [
                "type" => "D",
                "name" => $transfer->name,
            ];
        }

        $this->payments[] = [
            "type" => "E",
            "name" => "EFECTIVO",
            "amount" => "0.00",
            "date" => date("Y-m-d"),
            "operation_number" => "",
        ];
    }

    public function render()
    {

        $purchase = null;
        $document_payments = null;
        if($this->model_id != null){
            $purchase = Purchase::find($this->model_id);
            $document_payments = $purchase->document_payments;
            if(!$this->current_payment_id){
                foreach ($document_payments as $key => $document_payment) {
                    if($document_payment->status == "P"){
                        $this->current_payment_id = $document_payment->id;
                        $this->current_payment_name = $document_payment->name;
                        $this->current_payment_amount = $document_payment->total_amount - $document_payment->cash_amount - $document_payment->transfer_amount - $document_payment->card_amount;
                        $this->current_payment_symbol = $document_payment->currency->symbol;

                        $this->payments[0]["amount"] = sprintf('%g', $this->current_payment_amount);
                        break;
                    }
                }

            }
        }
        return view('livewire.purchase.purchase.payment-component')->with(compact('purchase', 'document_payments'));
    }

    public function toggle_modal($model_id = null){

        $this->open = !$this->open;
        if($model_id){
            $this->model_id = $model_id;
        }
        $this->current_payment_id = null;
        $this->current_payment_name = "";
        $this->current_payment_amount = "";
        $this->current_payment_symbol = "";
        $this->payments = [];
        $this->payments[] = [
            "type" => "E",
            "name" => "EFECTIVO",
            "amount" => "0.00",
            "date" => date("Y-m-d"),
            "operation_number" => "",
        ];
    }

    public function updatedPayments(){
        $total_no_cash = 0;
        foreach ($this->payments as $key => $value) {
            if(!$this->payments[$key]["amount"] || $this->payments[$key]["amount"] < "0"){
                $this->payments[$key]["amount"] = "0.00";
            }
            if($value["name"] != "EFECTIVO"){
                $total_no_cash = $total_no_cash + $this->payments[$key]["amount"];
            }
        }
        foreach ($this->payments as $key => $value) {
            if($value["name"] == "EFECTIVO"){
                /*$this->payments[$key]["amount"] = $this->current_payment_amount - $total_no_cash;
                if($this->payments[$key]["amount"] < 0){
                    $this->payments[$key]["amount"] = "0.00";
                }*/
            }
        }
    }

    public function select_payment_method($key){
        if(array_key_exists($key, $this->payment_methods)){
            foreach ($this->payments as $value) {
                if($value["name"] == $this->payment_methods[$key]["name"]){
                    $this->emit('alert-warning', [
                        'title' => 'Error!',
                        'text' => 'Ya seleccionó esa forma de pago.',
                    ]);
                    return true;
                }
            }
            $this->payments[] = [
                "type" => $this->payment_methods[$key]["type"],
                "name" => $this->payment_methods[$key]["name"],
                "amount" => "0.00",
            ];
            $this->updatedPayments();
        }
    }

    public function quit_method_item($key){
        if(array_key_exists($key, $this->payments)){
            if($this->payments[$key]["name"] == "EFECTIVO"){
                $this->payments[$key]["amount"] = "0.00";
            }else{
                unset($this->payments[$key]);
            }
            $this->updatedPayments();
        }
    }

    protected function calculate_payments($total_amount,$cash_amount,$card_amount,$transfer_amount){
        $status = "P";
        foreach ($this->payments as $key => $value) {
            if($value["type"] == "E"){
                $cash_amount = $cash_amount + $value["amount"];
            }
            if($value["type"] == "T"){
                $card_amount = $card_amount + $value["amount"];
            }
            if($value["type"] == "D"){
                $transfer_amount = $transfer_amount + $value["amount"];
            }
        }
        if($total_amount<=($cash_amount+$card_amount+$transfer_amount)){
            $status = "Y";
        }
        return [
            "cash_amount" => $cash_amount,
            "card_amount" => $card_amount,
            "transfer_amount" => $transfer_amount,
            "status" => $status
        ];
    }

    private function validate_payments(){
        $total = 0;
        foreach ($this->payments as $key => $value) {
            $total = $total + $value["amount"];
            if($value["amount"] < 0){
                $this->emit('alert-error', [
                    'title' => 'Error!',
                    'text' => 'Existe un monto menor a 0',
                ]);
                return false;
            }
            if($value["date"] ==""){
                $this->emit('alert-error', [
                    'title' => 'Error!',
                    'text' => 'Existe una fecha vacia',
                ]);
                return false;
            }

        }
        if(round($this->current_payment_amount, 2) < round($total, 2)){
            $this->emit('alert-error', [
                'title' => 'Error!',
                'text' => 'El monto pagado es mayor al total.',
            ]);
            return false;
        }
        return true;
    }

    public function save(){
        if($this->current_payment_id){
            if(!$this->validate_payments()){
                return true;
            }
            DB::beginTransaction();
            try {
                $document_payment = DocumentPayment::find($this->current_payment_id);
                if(!$document_payment){
                    throw new \Exception("No existe la cuota a pagar");
                }
                if($document_payment->status <> 'P'){
                    throw new \Exception("La cuota ya fue pagada o se encuentra eliminada");
                }

                $document_payment->update($this->calculate_payments($document_payment->total_amount,$document_payment->cash_amount,$document_payment->card_amount,$document_payment->transfer_amount));

                foreach ($this->payments as $key => $value) {
                    if(($value["amount"]+0) > 0){
                        $input_payment = [
                            'document_id' => $document_payment->document_id,
                            'document_payment_id' => $document_payment->id,
                            'currency_id' => $document_payment->currency_id,
                            'date_time' => $value["date"],
                            'type' => $value["type"],
                            'method_name' => $value["name"],
                            'amount' => $value["amount"],
                            'operation_number' => $value["operation_number"],
                            'status' => 'Y'
                        ];
                        Payment::create($input_payment);
                    }
                }

                $purchase = Purchase::find($document_payment->document_id);
                $document_payments = $purchase->document_payments;
                $status = "C";$pending=0;$paid=0;
                foreach ($document_payments as $key => $value) {
                    if($value->status=="P"){
                        $status = "P";
                    }
                    $paid = $value->cash_amount + $value->card_amount + $value->transfer_amount + $paid;
                }
                $pending = $purchase->total - $paid;
                if(round($pending)==0){
                    $status="C";
                }else{
                    $status="P";
                }
                $purchase->update(["status" => $status, "paid" => $paid, "pending_amount" => $pending]);


                DB::commit();

                $this->current_payment_id = null;
                $this->current_payment_name = "";
                $this->current_payment_amount = "";
                $this->current_payment_symbol = "";
                $this->payments = [];
                $this->payments[] = [
                    "type" => "E",
                    "name" => "EFECTIVO",
                    "amount" => "0.00",
                    "date" => date("Y-m-d"),
                    "operation_number" => "",
                ];

                $this->emit('search-and-reset-pages');

            } catch (\Exception $th) {

                DB::rollback();

                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'Algo salio mal: ' . $th->getMessage(),
                ]);
            }
        }
    }
}
