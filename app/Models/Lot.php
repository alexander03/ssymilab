<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'lot',
        'due_date',
        'status',
        'quantity',
        'remain',
        'product_id',
        'branch_office_id',
	    'barcode',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function scopeSearch($query, $product_id, $product_name, $lot, $status, $branch_office_id){

        $query = $query->join('products', 'products.id', '=', 'lots.product_id')
        ->leftjoin('brands','brands.id','=','products.brand_id')
        ->whereNull('products.deleted_at')
        ->where(function ($subquery) use ($product_name) {
            if (!is_null($product_name)) {
                $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%');
            }
        })->where(function ($subquery) use ($product_id) {
            if (!is_null($product_id)) {
                $subquery->where('lots.product_id', '=', $product_id);
            }
        })->where(function ($subquery) use ($lot) {
            if (!is_null($lot)) {
                $subquery->where('lots.lot','ILIKE', '%' . $lot . '%');
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('lots.status', '=', $status);
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id) && $branch_office_id!="") {
                $subquery->where('lots.branch_office_id', '=', $branch_office_id);
            }
        });

        return $query;
    }

}
