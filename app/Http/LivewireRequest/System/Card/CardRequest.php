<?php

namespace App\Http\LivewireRequest\System\Card;

class CardRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'name' => 'nombre',
        ];
    }

}
