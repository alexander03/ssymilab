<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ReferralGuide extends Document
{
    use SoftDeletes;

    protected $table = "documents";
    protected $attributes = [
       'module_id' => 2,
       'document_type_id' => 8
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('referal_guide', function ($query) {
            return $query->where('module_id', 2)->whereIn('document_type_id', [8]);
        });

    }

    public static function list_reasons(){
        return Reason::whereIn('document_type_id', [8])->orderBy('name')->get();
    }

    public function scopeSearch($query, $date_start, $date_end, $reason_id, $number, $supplier, $status, $branch_office_id){

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
                $subquery->where('date_time', '>=', $date_start." 00:00:00");
            }
        })
        ->where(function ($subquery) use ($date_end) {
            if (!is_null($date_end)) {
                $subquery->where('date_time', '<=', $date_end." 23:59:59");
            }
        })
        ->where(function ($subquery) use ($number) {
            if (!is_null($number)) {
                $subquery->where('number', 'ILIKE', '%' . $number . '%');
            }
        })->where(function ($subquery) use ($reason_id) {
            if (!is_null($reason_id)) {
                $subquery->where('documents.reason_id', '=', $reason_id);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id)) {
                $subquery->where('documents.branch_office_id', '=', $branch_office_id);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('documents.status', '=', $status);
            }
        });

        return $query;
    }


}
