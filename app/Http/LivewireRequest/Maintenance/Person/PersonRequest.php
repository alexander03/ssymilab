<?php

namespace App\Http\LivewireRequest\Maintenance\Person;

class PersonRequest{

    public $type;

    public function __construct($type){
        $this->type = $type;
    }

    public function rules(){
        return [
            'type' => ['required', 'string', 'max:1', 'in:1,6,4,0'],
            'dni' => ['nullable', 'string', 'min:8', 'max:8'],
            'ruc' => ['required_if:type,6', 'required_if:type,4', 'required_if:type,0', 'nullable', 'string', ($this->type == '6') ? 'max:11' : 'max:25', ($this->type == '6') ? 'min:11' : 'min:0'],
            'name' => ['required_if:type,1', 'nullable', 'string'],
            'surname_father' => ['nullable', 'string'],
            'surname_mother' => ['nullable', 'string'],
            'business_name' => ['required_if:type,6', 'nullable', 'string'],
            'address' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'numeric', 'distinct']
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'type' => "tipo de documento",
            'dni' => 'DNI',
            'ruc' => 'Documento',
            'name' => 'nombres',
            'surname_father' => 'apellido paterno',
            'surname_mother' => 'apellido materno',
            'business_name' => 'razón social',
            'address' => 'dirección',
            'email' => 'email',
            'phone' => 'Celular',
            'roles' => 'rol',
            'roles.*' => 'roles',
        ];
    }

}
