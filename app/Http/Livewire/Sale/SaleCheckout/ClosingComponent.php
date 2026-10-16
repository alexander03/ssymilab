<?php

namespace App\Http\Livewire\Sale\SaleCheckout;

use App\Http\LivewireRequest\Sale\SaleCheckout\CheckoutDocumentRequest;
use App\Http\Traits\Sale\SaleCheckoutTrait;
use App\Http\Traits\System\CheckoutTrait;
use App\Models\Checkout;
use App\Models\CheckoutDetail;
use App\Models\CheckoutDocument;
use App\Models\Currency;
use App\Models\DocumentPayment;
use App\Models\ExchangeRate;
use App\Models\Payment;
use App\Models\Reason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ClosingComponent extends Component
{
    use SaleCheckoutTrait;
    public $modal;
    public $event_modal;

    public $checkout;
    public $resume;

    public $date_time, $number, $comment;
    public $currencies;
    public $init_payments = [];

    public function mount(Checkout $checkout, bool $modal, string $event_modal)
    {
        $this->checkout = $checkout;
        $this->modal = $modal;
        $this->event_modal = $event_modal;

        $this->date_time = date('Y-m-d');
        $this->number = $this->getNextNumber(11);

        $this->currencies = Currency::order_currency()->get();
        foreach ($this->currencies as $key => $value) {
            if($value->base == "Y"){
                $exchange_rate = "1";
            }else{
                $exchange_rate = ExchangeRate::today($value->id);
                $exchange_rate = $exchange_rate ? $exchange_rate->value : "0.00";
            }
            $this->init_payments[] = [
                "currency_id" => $value->id,
                "currency_code" => $value->code,
                "currency_name" => $value->name,
                "currency_symbol" => $value->symbol,
                "currency_base" => $value->base,
                "exchange_rate" => $exchange_rate,
                "cash_amount" => "0.00"
            ];

            $this->resume[] = [
                "currency_id" => $value->id,
                "currency_code" => $value->code,
                "currency_name" => $value->name,
                "currency_symbol" => $value->symbol,
                "currency_base" => $value->base,
                "total_apertura" => 0.00,
                "total_apertura_efectivo" => 0.00,
                "total_apertura_tarjeta" => 0.00,
                "total_apertura_deposito" => 0.00,
                "total_ventas" => 0.00,
                "total_ventas_efectivo" => 0.00,
                "total_ventas_tarjeta" => 0.00,
                "total_ventas_deposito" => 0.00,
                "total_varios" => 0.00,
                "total_varios_efectivo" => 0.00,
                "total_varios_tarjeta" => 0.00,
                "total_varios_deposito" => 0.00,
                "total_egresos" => 0.00,
                "total_egresos_efectivo" => 0.00,
                "total_egresos_tarjeta" => 0.00,
                "total_egresos_deposito" => 0.00,
                "total_saldo" => 0.00,
                "total_saldo_efectivo" => 0.00,
                "total_saldo_tarjeta" => 0.00,
                "total_saldo_deposito" => 0.00,
            ];
        }

        $this->calculate_resume($this->checkout->id, "documents.id", "desc");

        foreach ($this->resume as $value) {
            foreach ($this->init_payments as $key => $item) {
                if($value["currency_id"] == $item["currency_id"]){
                    $this->init_payments[$key]["cash_amount"] = $value["total_saldo_efectivo"];
                }
            }
        }

    }


    public function render()
    {
        return view('livewire.sale.sale-checkout.closing-component');
    }

    /**
     * Función para regresar
     */
    public function back_button(){
        redirect()->route('sale-checkout');
    }

    public function save(CheckoutDocumentRequest $form_request){
        Validator::make(
            $this->inputs_validation(),
            $form_request->rules(),
            $form_request->messages(),
            $form_request->validationAttributes(),
        )->validate();

        DB::beginTransaction();
        try {
            foreach ($this->init_payments as $key => $item) {
                if($item["currency_base"] == "Y" || $item["cash_amount"] > 0){
                    $document_closing_input = [
                        "type" => "C",
                        "document_type_id" => 11,
                        "reason_id" => Reason::where('name', 'EGRESO FINAL')->first()->id,
                        'user_id' => Auth::user()->id,
                        'branch_office_id' => Auth::user()->branch_office_id,
                        'checkout_id' => $this->checkout->id,
                        "date_time" => date('Y-m-d H:i:s'),
                        "number" => $this->getNextNumber(11),
                        'currency_id' => $item["currency_id"],
                        'cash_amount' => $item["cash_amount"],
                        'card_amount' => 0,
                        'transfer_amount' => 0,
                        'total' => $item["cash_amount"],
                        'exchange_rate' => $item["exchange_rate"],
                        'detail' => json_encode(["comment" =>  ""]),
                        'dispatched' => 'Y'
                    ];

                    $document = CheckoutDocument::create($document_closing_input);
                    $payment_name = "PAGO ÚNICO";

                    $input_document_payment = [
                        'document_id' => $document->id,
                        'branch_office_id' => $document->branch_office_id,
                        'currency_id' => $item["currency_id"],
                        'name' => $payment_name,
                        'date_time' => $document->date_time,
                        'cash_amount' => $item["cash_amount"],
                        'card_amount' => 0,
                        'transfer_amount' => 0,
                        'total_amount' => $item["cash_amount"],
                        'exchange_rate' => $item["exchange_rate"],
                        'status' => 'Y'
                    ];
                    $document_payment = DocumentPayment::create($input_document_payment);
                    $input_payment = [
                        'document_id' => $document->id,
                        'document_payment_id' => $document_payment->id,
                        'currency_id' => $item["currency_id"],
                        'date_time' => $document->date_time,
                        'type' => "E",
                        'method_name' => "EFECTIVO",
                        'amount' => $item["cash_amount"],
                    ];
                    Payment::create($input_payment);

                }
            }


            $document_input =$this->input_create_closing();

            $closing = CheckoutDocument::create($document_input);

            $checkout = Checkout::find($this->checkout->id);
            $checkout->update(["status" => "C"]);

            $checkout_detail = Checkout::last_datail($checkout->id);

            $checkout_detail_input = [
                'close_date_time' => date('Y-m-d H:i:s'),
                'close_document_id' => $closing->id,
            ];

            $checkout_detail->update($checkout_detail_input);

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

    private function inputs_validation(){
        return [
            "date_time" => $this->date_time,
            "number" => $this->number,
            "comment" => $this->comment,
            'checkout_id' => $this->checkout->id,
            "init_payments" => $this->init_payments,
        ];
    }

    private function input_create_closing(){
        $detail = [
            "comment" => $this->comment ? $this->comment : ""
        ];
        return [
            "type" => "C",
            "document_type_id" => 11,
            "reason_id" => Reason::where('name', 'CIERRE')->first()->id,
            'user_id' => Auth::user()->id,
            'branch_office_id' => Auth::user()->branch_office_id,
            "date_time" => date('Y-m-d H:i:s'),
            'checkout_id' => $this->checkout->id,
            "number" => $this->getNextNumber(11),
            'detail' => json_encode($detail),
            'dispatched' => 'Y'
        ];
    }
}
