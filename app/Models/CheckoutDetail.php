<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'open_date_time',
        'close_date_time',
        'checkout_id',
        'open_document_id',
        'close_document_id',
        'user_id',
    ];

    public function checkout(){
        return $this->belongsTo(Checkout::class);
    }

    public function open_document(){
        return $this->belongsTo(Document::class, 'open_document_id');
    }

    public function close_document(){
        return $this->belongsTo(Document::class, 'close_document_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
