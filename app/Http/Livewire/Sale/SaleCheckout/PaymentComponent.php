<?php

namespace App\Http\Livewire\Sale\SaleCheckout;

use App\Http\Traits\Sale\SaleCheckoutTrait;
use App\Http\Traits\Maintenance\PersonTrait;
use App\Models\Card;
use App\Models\Checkout;
use App\Models\CheckoutDocument;
use App\Models\Currency;
use App\Models\DocumentPayment;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Models\Person;
use App\Models\TransferMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PaymentComponent extends Component
{
    use SaleCheckoutTrait;
    use PersonTrait;
    public $modal;
    public $event_modal;
    public $supplier;

    public $checkout;
    public $resume;

    public $date_time, $number, $comment, $document_type_id, $reason_id, $currency_id, $exchange_rate, $total;
    public $document_types;
    public $reasons;
    public $currencies;

    public $currency;
    public $current_payment_amount = "";

    public $payment_methods;
    public $payments;

    public $search_person;
    public $search_list_people;

    public $numero_documento;
    public $tipo_documento;
    public $fecha_documento;

    public function mount(Checkout $checkout, bool $modal, string $event_modal)
    {
        $this->checkout = $checkout;
        $this->modal = $modal;
        $this->event_modal = $event_modal;
        $this->search_list_people = [];

        $this->document_types = CheckoutDocument::list_document_types();
        $this->currencies = Currency::order_currency()->get();

        $this->currency = [
            "symbol" => $this->currencies[0]->symbol,
            "code" => $this->currencies[0]->code,
            "name" => $this->currencies[0]->name,
            "id" => $this->currencies[0]->id,
        ];

        $this->document_type_id = $this->document_types[0]->id;
        $this->reason_id = 10;
        $this->currency_id = $this->currencies[0]->id;
        $this->total = 0;
        $this->exchange_rate = "1";
        $this->date_time = date('Y-m-d');
        $this->fecha_documento = "";
        $this->number = $this->getNextNumber($this->document_type_id);

        $this->current_payment_amount = 0;
        $supplier = null;

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

        $this->supplier = [
            'id' => ($supplier) ? $supplier->id : "",
            'type' => ($supplier) ? $supplier->type : "6",
            'name_complete' => ($supplier) ? $supplier->getNameComplete() : "",
            'document_number' => ($supplier) ? $supplier->getDocumentNumber() : "",
        ];

        $this->search_person = $this->supplier["name_complete"];

        $this->payments[] = [
            "type" => "E",
            "name" => "EFECTIVO",
            "amount" => "0.00",
        ];

    }

    public function render()
    {
        return view('livewire.sale.sale-checkout.payment-component');
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('sale-checkout');
    }

    /**
     * Funciones de actualizacion de fields
     */
    public function updatedCurrencyId($value)
    {
        $currency = Currency::find($this->currency_id);
        if($currency){
            $this->currency = [
                "symbol" => $currency->symbol,
                "code" => $currency->code,
                "name" => $currency->name,
                "id" => $currency->id,
            ];
            if($currency->base == "Y"){
                $this->exchange_rate = "1";
            }else{
                $exchange_rate = ExchangeRate::today($currency->id);
                $this->exchange_rate = $exchange_rate ? $exchange_rate->value : "0.00";
            }
        }
    }

    public function updatedPayments(){
        $total = 0;
        foreach ($this->payments as $key => $value) {
            if(!$this->payments[$key]["amount"] || $this->payments[$key]["amount"] < "0"){
                $this->payments[$key]["amount"] = "0.00";
            }
            $total = $total + $this->payments[$key]["amount"];
        }
        $this->current_payment_amount = $total;
    }

    public function updatingDocumentTypeId($value)
    {
        $document_type = DocumentType::find($value);
        if($document_type){
            $this->number = $this->getNextNumber($this->document_type_id);
        }
        if($this->document_type_id == 10){
            $this->reason_id = 10;
        }else{
            $this->reason_id = 11;
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

    /**
     * Funciones para filtro de Personas
     */
    public function updatingSearchPerson($value)
    {
        if($value != ""){
            $this->search_list_people = $this->search_autocomplete($value,null, ['C','D','P','U','A']);
        }else{
            $this->search_list_people = [];
        }
    }

    public function select_active_person($person_id){
        $person = Person::find($person_id);
        if($person){
            $this->supplier = [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
            $this->search_person = $this->supplier["name_complete"];
        }
    }

    protected function calculate_payments(){
        $cash_amount = 0;
        $card_amount = 0;
        $transfer_amount = 0;
        $total_amount = 0;
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
            $total_amount = $total_amount + $value["amount"];
        }
        return [
            "cash_amount" => $cash_amount,
            "card_amount" => $card_amount,
            "transfer_amount" => $transfer_amount,
            "total_amount" => $total_amount,
            "status" => "Y"
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
        }
        return true;
    }

    public function save(){
            if(!$this->validate_payments()){
                return true;
            }
            DB::beginTransaction();
            try {
                $calculate_payments = $this->calculate_payments();
                $detail = [
                    "comment" => $this->comment ? $this->comment : "",
                    "tipo_documento" => $this->tipo_documento,
                    "numero_documento" => $this->numero_documento,
                    "fecha_documento" => $this->fecha_documento,
                ];
                $document_payment_input = [
                    "type" => "C",
                    "document_type_id" => $this->document_type_id,
                    "reason_id" => $this->reason_id,
                    'user_id' => Auth::user()->id,
                    'client_id' => $this->supplier["id"],
                    'branch_office_id' => Auth::user()->branch_office_id,
                    'checkout_id' => $this->checkout->id,
                    "date_time" => date('Y-m-d H:i:s'),
                    "number" => $this->getNextNumber($this->document_type_id),
                    'currency_id' => $this->currency_id,
                    'cash_amount' => $calculate_payments["cash_amount"],
                    'card_amount' => $calculate_payments["card_amount"],
                    'transfer_amount' => $calculate_payments["transfer_amount"],
                    'total' => $calculate_payments["total_amount"],
                    'exchange_rate' => $this->exchange_rate,
                    'detail' => json_encode($detail),
                    'dispatched' => 'Y'
                ];

                $document = CheckoutDocument::create($document_payment_input);
                $payment_name = "PAGO ÚNICO";

                $input_document_payment = [
                    'document_id' => $document->id,
                    'branch_office_id' => $document->branch_office_id,
                    'currency_id' => $this->currency_id,
                    'name' => $payment_name,
                    'date_time' => $document->date_time,
                    'cash_amount' => $calculate_payments["cash_amount"],
                    'card_amount' => $calculate_payments["card_amount"],
                    'transfer_amount' => $calculate_payments["transfer_amount"],
                    'total_amount' => $calculate_payments["total_amount"],
                    'exchange_rate' => $this->exchange_rate,
                    'status' => 'Y'
                ];
                $document_payment = DocumentPayment::create($input_document_payment);

                foreach ($this->payments as $key => $value) {
                    if($value["amount"] > 0){
                        $input_payment = [
                            'document_id' => $document_payment->document_id,
                            'document_payment_id' => $document_payment->id,
                            'currency_id' => $document_payment->currency_id,
                            'date_time' => date('Y-m-d H:i:s'),
                            'type' => $value["type"],
                            'method_name' => $value["name"],
                            'amount' => $value["amount"],
                            'status' => 'Y'
                        ];
                        Payment::create($input_payment);
                    }
                }



                DB::commit();

                Session::flash('success', 'Se registró correctamente');
                redirect()->route('sale-checkout');

            } catch (\Exception $th) {

                DB::rollback();

                $this->emit('alert-error', [
                    'title' => '¡Error!',
                    'text' => 'Algo salio mal: ' . $th->getMessage(),
                ]);
            }
    }
}
