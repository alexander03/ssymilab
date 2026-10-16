<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'document_id',
        'document_payment_id',
        'currency_id',
        'date_time',
        'type',
        'method_name',
        'amount',
	'operation_number',
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function document_payment(){
        return $this->belongsTo(DocumentPayment::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}
