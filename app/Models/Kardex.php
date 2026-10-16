<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kardex extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'quantity',
        'price',
        'product_id',
        'unit_id',
        'currency_id',
        'exchange_rate',
        'branch_office_id',
        'document_id',
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

    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function scopeSearch($query, $product_id, $product_name, $fechainicio, $fechafin, $branch_office_id){

        $query = $query->join('documents','documents.id','=','kardexes.document_id')
        ->join('people','people.id','=','documents.client_id')
	    ->join('products', 'products.id', '=', 'kardexes.product_id')->whereNull('products.deleted_at')
        ->leftjoin('brands','brands.id','=','products.brand_id')
        ->where(function ($subquery) use ($product_name) {
            if (!is_null($product_name)) {
                $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%');
            }
        })->where(function ($subquery) use ($product_id) {
            if (!is_null($product_id)) {
                $subquery->where('kardexes.product_id', '=', $product_id);
            }
        })->where(function ($subquery) use ($fechainicio) {
            if (!is_null($fechainicio) && $fechainicio!="") {
                $subquery->where('documents.date_time', '>=', $fechainicio." 00:00:00");
            }
        })->where(function ($subquery) use ($fechafin) {
            if (!is_null($fechafin) && $fechafin!="") {
                $subquery->where('documents.date_time', '<=', $fechafin." 23:59:59");
            }
        })->where(function ($subquery) use ($branch_office_id) {
            if (!is_null($branch_office_id)) {
                $subquery->where('kardexes.branch_office_id', '=', $branch_office_id);
            }
        });

        return $query;
    }

}
