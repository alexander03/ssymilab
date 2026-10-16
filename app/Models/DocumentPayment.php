<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DocumentPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'document_id',
        'branch_office_id',
        'currency_id',
        'name',
        'date_time',
        'schedule_date',
        'total_amount',
        'cash_amount',
        'card_amount',
        'transfer_amount',
        'exchange_rate',
        'status'
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function scopeSearch($query, $date_start, $date_end, $number, $supplier, $status, $branch_office_id, $module_id){

        $query = $query->join('documents', 'documents.id', '=', 'document_payments.document_id')
        ->join('people', 'people.id', '=', 'documents.client_id')
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
                $subquery->where('document_payments.schedule_date', '>=', $date_start);
            }
        })
        ->where(function ($subquery) use ($date_end) {
            if (!is_null($date_end)) {
                $subquery->where('document_payments.schedule_date', '<=', $date_end);
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
        })->where(function ($subquery) use ($status) {
            if (!is_null($status) && $status!="") {
                $subquery->where('documents.status', '=', $status);
            }
        })->where(function ($subquery) use ($module_id) {
            if (!is_null($module_id)) {
                $subquery->where('documents.module_id', '=', $module_id);
            }
        })->whereNotIn('document_payments.name', ['PAGO INICIAL', 'PAGO ÚNICO'])->whereNotIn('documents.status',['A']);

        return $query;
    }
}
