<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Purchase extends Document
{
    protected $table = "documents";
    protected $attributes = [
       'module_id' => 1,
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('purchase', function ($query) {
            return $query->where('module_id', 1)->whereIn('document_type_id', [2, 3]);
        });

    }

    public static function list_document_types(){
        return DocumentType::whereIn('id', [2, 3])->get();
    }

    public function scopeSearch($query, $date_start, $date_end, $document_type_id, $number, $supplier, $status, $branch_office_id){

        $query = $query->join('people', 'people.id', '=', 'documents.client_id')
        ->where(function ($subquery) use ($supplier) {
            if (!is_null($supplier)) {
                $subquery->where('people.name', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.surname_father', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.surname_mother', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.business_name', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.dni', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.ruc', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere(DB::raw("CONCAT(people.name, ' ', people.surname_father, ' ', people.surname_mother)"), 'ILIKE', '%' . $supplier . '%')
                    ->orWhere(DB::raw("CONCAT(people.surname_father, ' ', people.surname_mother, ' ', people.name)"), 'ILIKE', '%' . $supplier . '%');
            }
        })
        ->where(function ($subquery) use ($date_start) {
            if (!is_null($date_start)) {
                $subquery->where('date_time', '>=', $date_start);
            }
        })
        ->where(function ($subquery) use ($date_end) {
            if (!is_null($date_end)) {
                $subquery->where('date_time', '<=', $date_end);
            }
        })
        ->where(function ($subquery) use ($number) {
            if (!is_null($number)) {
                $subquery->where('number', 'ILIKE', '%' . $number . '%');
            }
        })->where(function ($subquery) use ($document_type_id) {
            if (!is_null($document_type_id)) {
                $subquery->where('documents.document_type_id', '=', $document_type_id);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id) && $branch_office_id!="") {
                $subquery->where('documents.branch_office_id', '=', $branch_office_id);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('documents.status', '=', $status);
            }
        });

        return $query;
    }

    public function scopeSearchDetail($query, $date_start, $date_end, $document_type_id, $number, $supplier, $product, $status, $branch_office_id){

        $query = $query->join('people', 'people.id', '=', 'documents.client_id')
        ->join('document_details','document_details.document_id','=','documents.id')
        ->join('products','products.id','=','document_details.product_id')
        ->where(function ($subquery) use ($supplier) {
            if (!is_null($supplier)) {
                $subquery->where('people.name', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.surname_father', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.surname_mother', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.business_name', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.dni', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere('people.ruc', 'ILIKE', '%' . $supplier . '%')
                    ->orWhere(DB::raw("CONCAT(people.name, ' ', people.surname_father, ' ', people.surname_mother)"), 'ILIKE', '%' . $supplier . '%')
                    ->orWhere(DB::raw("CONCAT(people.surname_father, ' ', people.surname_mother, ' ', people.name)"), 'ILIKE', '%' . $supplier . '%');
            }
        })
        ->where(function ($subquery) use ($product) {
            if (!is_null($product)) {
                $subquery->where(DB::raw("CONCAT(products.name,' ',products.presentation,' ',products.model)"), 'ilike', '%'.$product.'%');
            }
        })
        ->where(function ($subquery) use ($date_start) {
            if (!is_null($date_start)) {
                $subquery->where('date_time', '>=', $date_start);
            }
        })
        ->where(function ($subquery) use ($date_end) {
            if (!is_null($date_end)) {
                $subquery->where('date_time', '<=', $date_end);
            }
        })
        ->where(function ($subquery) use ($number) {
            if (!is_null($number)) {
                $subquery->where('number', 'ILIKE', '%' . $number . '%');
            }
        })->where(function ($subquery) use ($document_type_id) {
            if (!is_null($document_type_id)) {
                $subquery->where('documents.document_type_id', '=', $document_type_id);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id) && $branch_office_id!="") {
                $subquery->where('documents.branch_office_id', '=', $branch_office_id);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('documents.status', '=', $status);
            }
        })
        ->whereNotIn('documents.status',['A']);

        return $query;
    }
}
