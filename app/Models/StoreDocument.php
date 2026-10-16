<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreDocument extends Document
{
    use SoftDeletes;

    protected $table = "documents";
    protected $attributes = [
       'module_id' => 2,
    ];

    protected static function boot(){

        parent::boot();

        static::addGlobalScope('purchase', function ($query) {
            return $query->where('module_id', 2)->whereIn('document_type_id', [6, 7]);
        });

    }

    public static function list_document_types(){
        return DocumentType::whereIn('id', [6, 7])->get();
    }

    public function scopeSearch($query, $date_start, $date_end, $document_type_id, $number, $status, $branch_office_id){

        $query = $query
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
