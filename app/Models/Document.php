<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'module_id',
        'document_type_id',
        'reason_id',
        'client_id',
        'shipper_id',
        'user_id',
        'branch_office_id',
        'checkout_id',
        'document_id',
        'currency_id',
        'ubigeo_start_code',
        'ubige_end_code',
        'type',
        'number',
        'date_time',
        'schedule_time',
        'total_taxed',
        'total_exonerated',
        'total_unaffected',
        'igv',
        'icbper',
        'global_discount',
        'discount',
        'total',
        'exchange_rate',
        'cash_amount',
        'card_amount',
        'transfer_amount',
        'pending_amount',
        'paid',
        'money_back',
        'weight',
        'detail',
        'dispatched',
        'status',
	    'person_id',
        'other',
        'flete',
        'seguro',
        'paid_comision',
        'percentage_comision',
    ];

    public function module(){
        return $this->belongsTo(Module::class);
    }

    public function document_type(){
        return $this->belongsTo(DocumentType::class);
    }

    public function reason(){
        return $this->belongsTo(Reason::class);
    }

    public function client(){
        return $this->belongsTo(Person::class, 'client_id')->withTrashed();
    }

    public function shipper(){
        return $this->belongsTo(Person::class, 'shipper_id');
    }

    public function vendedor(){
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function checkout(){
        return $this->belongsTo(Checkout::class);
    }

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function ubigeo_start(){
        return $this->belongsTo(District::class, 'ubigeo_start_code', 'code');
    }

    public function ubigeo_end(){
        return $this->belongsTo(District::class, 'ubigeo_end_code', 'code');
    }

    public function details(){
        return $this->hasMany(DocumentDetail::class, 'document_id');
    }

    public function document_payments_all(){
        return $this->hasMany(DocumentPayment::class, 'document_id')->orderBy('id', 'ASC');
    }

    public function document_payments(){
        return $this->hasMany(DocumentPayment::class, 'document_id')->whereNotIn('document_payments.status', ['A'])->orderBy('id', 'ASC');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'document_id');
    }

    public function comment(){
        $details = ($this->detail && trim($this->detail) != "") ? json_decode($this->detail, true) : [];
        return (array_key_exists('comment', $details)) ? $details["comment"] : "";
    }
}
