<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Schedule extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'installed_date',
        'duration',
        'product_serie_id',
        'product_status',
        'customer_name',
        'product_place',
        'product_location',
	    'status',
        'customer_id',
    ];

    public function product_serie(){
        return $this->belongsTo(ProductSerie::class);
    }

    public function customer(){
        return $this->belongsTo(Person::class,'customer_id');
    }

    public function product_maintenances(){
        return $this->hasMany(ProductMaintenance::class);
    }

    public function scopeSearch($query, $date_start, $date_end, $product_name, $product_serie, $customer_name, $product_status, $status, $brand_id = null, $product_model = null){

        $query = $query->join('product_series', 'product_series.id', '=', 'schedules.product_serie_id')
        ->join('products', 'products.id', '=', 'product_series.product_id')
        ->join(DB::raw('people as person'),'person.id','=','schedules.customer_id')
        ->where(function ($subquery) use ($product_name) {
            if (!is_null($product_name) && $product_name!="") {
                $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%');
            }
        })
        ->where(function ($subquery) use ($product_model) {
            if (!is_null($product_model) && $product_model!="") {
                $subquery->where('products.model', 'ILIKE', '%' . $product_model . '%');
            }
        })
        ->where(function ($subquery) use ($date_start) {
            if (!is_null($date_start) && $date_start!="") {
                $subquery->where('schedules.installed_date','>=',$date_start);
            }
        })
        ->where(function ($subquery) use ($date_end) {
            if (!is_null($date_end) && $date_end!="") {
                $subquery->where('schedules.installed_date','<=',$date_end);
            }
        })
        ->where(function ($subquery) use ($product_serie) {
            if (!is_null($product_serie) && $product_serie!="") {
                $subquery->where('product_series.serie','like','%'.$product_serie.'%');
            }
        })
        ->where(function ($subquery) use ($product_status) {
            if (!is_null($product_status) && $product_status!="") {
                $subquery->where('schedules.product_status','like','%'.$product_status.'%');
            }
        })
        ->where(function ($subquery) use ($status) {
            if (!is_null($status) && $status!="") {
                $subquery->where('schedules.status','like','%'.$status.'%');
            }
        })
        ->where(function ($subquery) use ($brand_id) {
            if (!is_null($brand_id) && $brand_id!="") {
                $subquery->where('products.brand_id','=',$brand_id);
            }
        })
        ->where(function ($subquery) use ($customer_name) {
            if (!is_null($customer_name)) {
                $subquery->where('person.name', 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere('person.surname_father', 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere('person.surname_mother', 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere('person.business_name', 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere('person.dni', 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere('person.ruc', 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere(DB::raw("CONCAT(person.name, ' ', person.surname_father, ' ', person.surname_mother)"), 'ILIKE', '%' . $customer_name . '%')
                    ->orWhere(DB::raw("CONCAT(person.surname_father, ' ', person.surname_mother, ' ', person.name)"), 'ILIKE', '%' . $customer_name . '%');
            }
        });

        return $query;
    }
}
