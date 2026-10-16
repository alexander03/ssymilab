<?php

namespace App\Http\LivewireRequest\Users\User;

class UserRequestCreate{

    public function rules(){
        return [
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'branch_office_id' => ['required', 'integer', 'exists:branch_offices,id'],
            'user_type_id' => ['required', 'integer', 'exists:user_types,id'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ];
    }

    public function messages(){
        return [
        ];
    }

    public function validationAttributes(){
        return [
            'username' => 'nombre de usuario',
            'person_id' => 'persona',
            'branch_office_id' => 'sucursal',
            'user_type_id' => 'tipo de usuario',
            'password' => 'contraseña',
        ];
    }

}
