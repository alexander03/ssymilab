<?php

namespace App\Http\LivewireRequest\Maintenance\Reason;

class ReasonRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
            'document_type_id' => ['required', 'integer'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'name' => 'nombre',
            'document_type_id' => 'tipo documento',
        ];
    }

}
