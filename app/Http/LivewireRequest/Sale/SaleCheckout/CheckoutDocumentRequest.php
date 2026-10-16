<?php

namespace App\Http\LivewireRequest\Sale\SaleCheckout;

class CheckoutDocumentRequest{

    public function rules(){
        return [
            'date_time' => ['required', 'string'],
            'number' => ['required', 'string'],
            'checkout_id' => ['required', 'integer', 'exists:checkouts,id'],
            'init_payments' => ['required', 'array'],
            'init_payments.*.currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'init_payments.*.cash_amount' => ['required', 'numeric', 'gte:0'],
            'init_payments.*.exchange_rate' => ['required', 'numeric', 'gte:0'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'date_time' => 'fecha',
            'number' => 'número',
            'init_payments' => 'lista de monedas',
            'init_payments.*.currency_id' => 'identificador de moneda',
            'init_payments.*.cash_amount' => 'monto inicial',
            'init_payments.*.exchange_rate' => 'tipo de cambio',
        ];
    }

}
