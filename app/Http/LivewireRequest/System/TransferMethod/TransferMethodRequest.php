<?php

namespace App\Http\LivewireRequest\System\TransferMethod;

class TransferMethodRequest{

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
