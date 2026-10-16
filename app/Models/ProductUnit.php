<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'formula',
        'purchase_price',
        'sell_price',//
        'whosale_price',
        'branch_office_id',
        'unit_id',
        'product_id',
        'base_unit_id',
        'currency_id',//
        'sell_price_min',
        'win_percentage',
        'win_percentage_min',
    ];

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function base_unit(){
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function currency(){
        return $this->belongsTo(Currency::class);
    }
}
