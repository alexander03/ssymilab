<?php

namespace App\Http\LivewireRequest\Purchase\Requirement;

class RequirementRequest{

    public function rules(){
        return [
            'client_id' => ['required', 'integer', 'exists:people,id'],
            'number' => ['required', 'string'],
            'comment' => ['nullable', 'string'],
            'shopping_car' => ['required', 'array', 'min:1'],
            'shopping_car.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'shopping_car.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'shopping_car.*.quantity' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'client_id' => "proveedor",
            'number' => 'número',
            'comment' => 'comentario',
            'shopping_car' => 'lista de productos',
            'shopping_car.*.quantity' => 'cantidad',
        ];
    }

}
