<?php

namespace App\Http\LivewireRequest\Security\Action;

class ActionRequest{

    public function rules(){
        return [
            'menu_option_id' => ['required', 'integer', 'exists:menu_options,id'],
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
            'route' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'menu_option_id' => 'menu categoría',
            'name' => 'nombre',
            'code' => 'código',
            'route' => 'ruta',
            'description' => 'descripción'
        ];
    }

}
