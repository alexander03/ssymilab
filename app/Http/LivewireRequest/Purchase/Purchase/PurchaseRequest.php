<?php

namespace App\Http\LivewireRequest\Purchase\Purchase;

class PurchaseRequest{

    public function rules(){
        return [
            'document_type_id' => ['required', 'integer', 'exists:document_types,id'],
            'client_id' => ['required', 'integer', 'exists:people,id'],
            'document_id' => ['nullable', 'integer', 'exists:documents,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'type' => ['required', 'string', 'in:C,D'],
            'schedule_time' => ['nullable', 'date'],
            'date_time' => ['required', 'date'],
            'number' => ['required', 'string'],
            'total' => ['required', 'numeric', 'gte:0'],
            'exchange_rate' => ['required', 'numeric', 'gte:0'],
            'pending_amount' => ['required', 'numeric', 'gte:0'],
            'comment' => ['nullable', 'string'],
            'shopping_car' => ['required', 'array', 'min:1'],
            'shopping_car.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'shopping_car.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'shopping_car.*.quantity' => ['required', 'numeric', 'gt:0'],
            'shopping_car.*.purchase_price' => ['required', 'numeric', 'gte:0'],
            'payments' => ['nullable', 'array'],
            'payments.*.type' => ['required', 'string', 'in:E,T,D'],
            'payments.*.name' => ['required', 'string'],
            'payments.*.amount' => ['required', 'numeric', 'gte:0'],
            'document_payments' => ['nullable', 'array'],
            'document_payments.*.payment_name' => ['required', 'string'],
            'document_payments.*.schedule_date' => ['required', 'date'],
            'document_payments.*.total_amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(){
        return [];
    }

    public function validationAttributes(){
        return [
            'document_type_id' => "tipo de documento",
            'client_id' => "proveedor",
            'document_id' => "requerimiento",
            'currency_id' => "moneda",
            'type' => "tipo de pago",
            'schedule_time' => "fecha de entrega",
            'number' => 'número',
            'total' => 'total',
            'exchange_rate' => 'tipo de cambio',
            'pending_amount' => 'monto pendiente de pago',
            'comment' => 'comentario',
            'shopping_car' => 'lista de productos',
            'shopping_car.*.quantity' => 'cantidad',
            'shopping_car.*.purchase_price' => 'precio de compra',
            'document_payments' => 'lista de cuotas',
            'document_payments.*.schedule_date' => 'fecha de cuota',
            'document_payments.*.total_amount' => 'monto por cuota',
        ];
    }

}
