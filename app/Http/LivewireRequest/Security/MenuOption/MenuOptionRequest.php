<?php

namespace App\Http\LivewireRequest\Security\MenuOption;

class MenuOptionRequest{

    public function rules(){
        return [
            'menu_category_id' => ['required', 'integer', 'exists:menu_categories,id'],
            'name' => ['required', 'string'],
            'link' => ['required', 'string'],
            'order' => ['required', 'integer']
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'menu_category_id' => 'menu categoría',
            'name' => 'nombre',
            'icon' => 'ícono',
            'order' => 'orden'
        ];
    }

}
