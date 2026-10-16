<?php

namespace App\Http\LivewireRequest\Maintenance\Unit;

class UnitRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'name' => 'nombre',
            'code' => 'código',
        ];
    }

}
