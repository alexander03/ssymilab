<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'period'
    ];

    public function jobs(){
        return $this->hasMany(Job::class)->orderBy('jobs.id', 'ASC');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function scopeSearch($query, $product_name){

        $query = $query->rightJoin('products', 'products.id', '=', 'maintenances.product_id')
        ->leftjoin('brands','brands.id','=','products.brand_id')
        ->where('products.type', '=', 'M')
        ->whereNull('products.deleted_at')
        ->where(function ($subquery) use ($product_name) {
            if (!is_null($product_name)) {
                $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%');
            }
        });

        return $query;
    }
}
