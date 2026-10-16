<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSerie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'serie',
        'status',
        'product_id',
        'branch_office_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function scopeSearch($query, $product_id, $product_name, $serie, $status, $branch_office_id, $productserie='', $product_serie_id=null, $product_type=''){

        $query = $query->join('products', 'products.id', '=', 'product_series.product_id')
        ->leftjoin('brands','brands.id','=','products.brand_id')
        ->whereNull('products.deleted_at')
        ->where(function ($subquery) use ($product_name) {
            if (!is_null($product_name) && $product_name!="") {
                $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%')
                        ->orWhere('products.model', 'ILIKE', '%' . $product_name . '%')
                        ->orWhere('products.code', 'ILIKE', '%' . $product_name . '%');
            }
        })->where(function ($subquery) use ($product_id) {
            if (!is_null($product_id)) {
                $subquery->where('product_series.product_id', '=', $product_id);
            }
        })->where(function ($subquery) use ($serie) {
            if (!is_null($serie) && $serie!="") {
                $subquery->where('product_series.serie','ILIKE', '%' . $serie. '%');
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('product_series.status', '=', $status);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id) && $branch_office_id!="") {
                $subquery->where('product_series.branch_office_id', '=', $branch_office_id);
            }
        })->where(function ($subquery) use ($productserie) {
            if (!is_null($productserie) && $productserie!='') {
                $subquery->where('products.name', 'ILIKE', '%' . $productserie . '%')->orWhere('product_series.serie','like','%'.$productserie.'%');
            }
        })->where(function ($subquery) use ($product_serie_id) {
            if (!is_null($product_serie_id)) {
                $subquery->where('product_series.id', '=', $product_serie_id);
            }
        })->where(function ($subquery) use ($product_type) {
            if (!is_null($product_type) && $product_type!="") {
                $subquery->where('products.type', 'like', $product_type);
            }
        });

        return $query;
    }

}
