<?php

namespace App\Http\LivewireRequest\Security\MenuCategory;

class MenuCategoryRequest{

    public function rules(){
        return [
            'name' => ['required', 'string'],
            'icon' => ['required', 'string'],
            'order' => ['required', 'integer']
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'name' => 'nombre',
            'icon' => 'ícono',
            'order' => 'orden'
        ];
    }

}
