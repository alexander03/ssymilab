<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Document
{
    use SoftDeletes;

    protected $table = "documents";
    protected $attributes = [
       'module_id' => 4,
       'document_type_id' => 15
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('sale', function ($query) {
            return $query->where('module_id', 4)->whereIn('document_type_id', [15]);
        });

    }

    public function scopeSearch($query, $date_start, $date_end, $number, $supplier, $status, $branch_office_id){

        $query = $query->join('people', 'people.id', '=', 'documents.client_id')
        ->join('users', 'users.id', '=', 'documents.user_id')
        ->join('people as people_user', 'people_user.id', '=', 'users.person_id')
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
