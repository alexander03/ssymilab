<?php

namespace App\Http\LivewireRequest\Maintenance\Product;

class ProductRequest{

    public function rules(){
        return [
            'type' => ['required', 'string', 'max:1', 'in:M,R,S,E,C,O'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'supplier_id' => ['required', 'integer', 'exists:people,id'],
            'code' => ['nullable', 'string'],
            'barcode' => ['nullable', 'string'],
            'name' => ['required', 'string'],
            'specifications' => ['nullable', 'string'],
            'active_ingredients' => ['nullable', 'array'],
            'active_ingredients.*.id' => ['required', 'integer', 'exists:active_ingredients,id'],
            'base_unit_id' => ['required', 'integer', 'exists:units,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'sell_price' => ['required', 'numeric', 'gt:0'],
            'igv' => ['required', 'string', 'max:2', 'in:10,20,30'],
            'affects_stock' => ['required', 'max:1', 'in:Y,N'],
            'security_stock' => ['required_if:affects_stock,Y', 'nullable', 'numeric'],
            'image' => ['nullable', 'image'],
            'product_units' => ['nullable', 'array'],
            'product_units.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'product_units.*.currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'product_units.*.formula' => ['required', 'numeric', 'gt:0'],
            'product_units.*.sell_price' => ['required', 'numeric', 'gt:0']
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'type' => "tipo de producto",
            'category_id' => 'categoría',
            'brand_id' => 'marca',
            'supplier_id' => 'proveedor',
            'code' => 'código de producto',
            'barcode' => 'código de barra',
            'name' => 'nombre',
            'specifications' => 'especificaciones',
            'active_ingredients' => 'principios activos',
            'active_ingredients.*.id' => 'identificador de principio activo',
            'base_unit_id' => 'unidad base',
            'currency_id' => 'moneda base',
            'sell_price' => 'precio base',
            'igv' => 'afectación igv',
            'affects_stock' => 'afecta stock',
            'security_stock' => 'stock minimo',
            'image' => 'Imágen',
            'product_units' => 'presentaciones',
            'product_units.*.unit_id' => 'identificador de unidad',
            'product_units.*.currency_id' => 'identificador de moneda',
            'product_units.*.formula' => 'equivalencia',
            'product_units.*.sell_price' => 'precio de venta',
        ];
    }

}
