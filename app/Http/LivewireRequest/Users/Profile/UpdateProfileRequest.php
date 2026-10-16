<?php

namespace App\Http\LivewireRequest\Users\Profile;

class UpdateProfileRequest{

    public function rules(){
        return [
            'type' => ['required', 'string', 'max:1', 'in:1,6'],
            'dni' => ['nullable', 'string', 'min:8', 'max:8'],
            'ruc' => ['required_if:type,6', 'nullable', 'string', 'max:11', 'min:11'],
            'name' => ['required_if:type,1', 'nullable', 'string'],
            'surname_father' => ['required_if:type,1', 'nullable', 'string'],
            'surname_mother' => ['nullable', 'string'],
            'business_name' => ['required_if:type,6', 'nullable', 'string'],
            'address' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],

        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'type' => "tipo de documento",
            'dni' => 'DNI',
            'ruc' => 'RUC',
            'name' => 'nombres',
            'surname_father' => 'apellido paterno',
            'surname_mother' => 'apellido materno',
            'business_name' => 'razón social',
            'address' => 'dirección',
            'email' => 'email',
            'phone' => 'Celular',
        ];
    }

}
