<?php

namespace App\Http\LivewireRequest\Maintenance\Zone;

class ZoneRequest{

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
