<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPrice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'product_id',
        'unit_id',
        'currency_id',
        'sell_price'
    ];

    public function customer(){
        return $this->belongsTo(Person::class, 'customer_id');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}
