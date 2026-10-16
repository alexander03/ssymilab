<?php

namespace App\Http\LivewireRequest\System\Checkout;

class CheckoutRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
            'serie' => ['required', 'string'],
            'serie_note' => ['required', 'string'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'name' => 'nombre',
            'serie' => 'serie de boletas y facturas',
            'serie_note' => 'serie de notas',
        ];
    }

}
