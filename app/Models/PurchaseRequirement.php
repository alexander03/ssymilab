<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class PurchaseRequirement extends Document
{
    protected $table = "documents";
    protected $attributes = [
       'module_id' => 1,
       'document_type_id' => 1,
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('requirement_purchase', function ($query) {
            return $query->where('module_id', 1)->where('document_type_id', 1);
        });

    }

    public function scopeSearch($query, $date_start, $date_end, $number, $supplier, $branch_office_id){

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
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id) && $branch_office_id!="") {
                $subquery->where('documents.branch_office_id', '=', $branch_office_id);
            }
        });

        return $query;
    }



}
