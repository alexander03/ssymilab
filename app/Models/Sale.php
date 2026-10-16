<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sale extends Document
{
    use SoftDeletes;

    protected $table = "documents";
    protected $attributes = [
       'module_id' => 4
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('sale', function ($query) {
            return $query->where('module_id', 4)->whereIn('document_type_id', [12,13]);
        });

    }

    public static function list_document_types(){
        return DocumentType::whereIn('id', [12, 13])->get();
    }

    public function scopeSearch($query, $date_start, $date_end, $document_type_id, $number, $supplier, $status, $branch_office_id,$vendedor, $supervisor=''){

        $query = $query->join('people', 'people.id', '=', 'documents.client_id')
                    ->join(DB::raw('people as person'),'person.id','=','documents.person_id')
                    ->leftjoin(DB::raw('people as supervisor'),'supervisor.id','=','documents.supervisor_id')
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
        ->where(function ($subquery) use ($vendedor) {
            if (!is_null($vendedor) && $vendedor!="") {
                $subquery->where('person.name', 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere('person.surname_father', 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere('person.surname_mother', 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere('person.business_name', 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere('person.dni', 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere('person.ruc', 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere(DB::raw("CONCAT(person.name, ' ', person.surname_father, ' ', person.surname_mother)"), 'ILIKE', '%' . $vendedor . '%')
                    ->orWhere(DB::raw("CONCAT(person.surname_father, ' ', person.surname_mother, ' ', person.name)"), 'ILIKE', '%' . $vendedor . '%');
            }
        })
        ->where(function ($subquery) use ($supervisor) {
            if (!is_null($supervisor) && $supervisor!="") {
                $subquery->where('supervisor.name', 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere('supervisor.surname_father', 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere('supervisor.surname_mother', 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere('supervisor.business_name', 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere('supervisor.dni', 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere('supervisor.ruc', 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere(DB::raw("CONCAT(supervisor.name, ' ', supervisor.surname_father, ' ', supervisor.surname_mother)"), 'ILIKE', '%' . $supervisor . '%')
                    ->orWhere(DB::raw("CONCAT(supervisor.surname_father, ' ', supervisor.surname_mother, ' ', supervisor.name)"), 'ILIKE', '%' . $supervisor . '%');
            }
        })
        ->where(function ($subquery) use ($date_start) {
            if (!is_null($date_start) && $date_start!="") {
                $subquery->where('date_time', '>=', $date_start.' 00:00:00');
            }
        })
        ->where(function ($subquery) use ($date_end) {
            if (!is_null($date_end) && $date_end!="") {
                $subquery->where('date_time', '<=', $date_end.' 23:59:59');
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
            if (!is_null($branch_office_id)) {
                $subquery->where('documents.branch_office_id', '=', $branch_office_id);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status) && $status!="") {
                $subquery->where('documents.status', 'like', $status);
            }
        });

        return $query;
    }
}
