<?php

namespace App\Http\LivewireRequest\System\Company;

class CompanyRequest{

    public function rules(){
        return [
            'ruc' => ['required', 'string', 'min:11', 'max:11'],
            'business_name' => ['required', 'string'],
            'trade_name' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'password' => ['nullable', 'string'],
            'authentication_mode' => ['nullable', 'in:P'],
            'username_sunat' => ['nullable', 'string'],
            'password_sunat' => ['nullable', 'string'],
            'department_id' => ['required', 'string', 'exists:departments,code'],
            'province_id' => ['required', 'string', 'exists:provinces,code'],
            'district_id' => ['required', 'string', 'exists:districts,code'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'ruc' => 'RUC',
            'business_name' => 'razón social',
            'trade_name' => 'nombre comercial',
            'address' => 'dirección fiscal',
            'password' => 'contraseña de empresa',
            'authentication_mode' => 'modo de autenticación',
            'username_sunat' => 'usuario de SUNAT',
            'password_sunat' => 'contraseña de SUNAT',
            'department_id' => 'departamento',
            'province_id' => 'provincia',
            'district_id' => 'distrito',
        ];
    }

}
