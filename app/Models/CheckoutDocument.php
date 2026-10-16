<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckoutDocument extends Document
{
    use SoftDeletes;

    protected $table = "documents";
    protected $attributes = [
       'module_id' => 4
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('proforma', function ($query) {
            return $query->where('module_id', 4)->whereIn('document_type_id', [10, 11]);
        });

    }

    public static function list_document_types(){
        return DocumentType::whereIn('id', [10, 11])->get();
    }

    public function scopeSearch($query, $checkout_id, $open_document_id, $close_document_id, $date_start, $date_end, $supplier, $status, $branch_office_id){

        $query = $query->leftjoin('people', 'people.id', '=', 'documents.client_id')
        ->where(function ($subquery) use ($checkout_id) {
            if (!is_null($checkout_id)) {
                $subquery->where('documents.checkout_id', '=', $checkout_id);
            }
        })
        ->where(function ($subquery) use ($open_document_id) {
            if (!is_null($open_document_id)) {
                $subquery->where('documents.id', '>=', $open_document_id);
            }
        })
        ->where(function ($subquery) use ($close_document_id) {
            if (!is_null($close_document_id)) {
                $subquery->where('documents.id', '<=', $close_document_id);
            }
        })
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
        ->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id)) {
                $subquery->where('documents.branch_office_id', '=', $branch_office_id);
            }
        })
        ->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('documents.status', '=', $status);
            }
        });

        return $query;
    }
}
