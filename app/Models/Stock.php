<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'real',
        'separated',
        'available',
        'product_id',
        'unit_id',
        'branch_office_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public static function verificar_existencia($product_id, $branch_office_id){

        return Stock::where('product_id', $product_id)->where('branch_office_id', $branch_office_id)->first();

    }

    public function scopeSearch($query, $product_id, $product_name, $branch_office_id, $type){

        $query = $query->join('products', 'products.id', '=', 'stocks.product_id')->whereNull('products.deleted_at')
        ->where(function ($subquery) use ($product_name) {
            if (!is_null($product_name)) {
                $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%')
                        ->orWhere('products.model', 'ILIKE', '%' . $product_name . '%');
            }
        })->where(function ($subquery) use ($product_id) {
            if (!is_null($product_id)) {
                $subquery->where('stocks.product_id', '=', $product_id);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id) && $branch_office_id!="") {
                $subquery->where('stocks.branch_office_id', '=', $branch_office_id);
            }
        })->where(function ($subquery) use ($type) {
            if (!is_null($type) && $type!="") {
                $subquery->where('products.type', 'like', $type);
            }
        });

        return $query;
    }
}
