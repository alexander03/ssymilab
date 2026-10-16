<?php

namespace App\Http\LivewireRequest\Users\UserType;

class UserTypeRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
            'code' => ['required', 'string' , 'max:2'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'name' => 'nombre',
            'code' => 'código'
        ];
    }

}
