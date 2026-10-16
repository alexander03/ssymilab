<?php

namespace App\Http\LivewireRequest\System\BranchOffice;

class BranchOfficeRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
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
            'name' => 'nombre',
            'address' => 'dirección',
            'phone' => 'teléfono',
            'company_id' => 'identificador de empresa',
            'department_id' => 'departamento',
            'province_id' => 'provincia',
            'district_id' => 'distrito',
        ];
    }

}
