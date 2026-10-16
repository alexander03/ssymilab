<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductMaintenance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'schedule_id',
        'product_serie_id',
        'type',
        'scheduled_date',
        'done_date',
        'status',
        'head_name',
        'staff_name',
        'observations',
    ];

    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }

    public function product_serie(){
        return $this->belongsTo(ProductSerie::class);
    }

    public function maintenance_files(){
        return $this->hasMany(MaintenanceFile::class);
    }

    public function scopeSearch($query, $schedule_id, $date_start, $date_end, $product_name, $product_serie, $customer_name){

        $query = $query->join('schedules','schedules.id','=','product_maintenances.schedule_id')
            ->join('product_series', 'product_series.id', '=', 'product_maintenances.product_serie_id')
            ->join('products', 'products.id', '=', 'product_series.product_id')
            ->join(DB::raw('people as person'),'person.id','=','schedules.customer_id')
            ->where(function ($subquery) use ($schedule_id) {
                if (!is_null($schedule_id)) {
                    $subquery->where('schedule_id', '=', $schedule_id);
                }
            })
            ->where(function ($subquery) use ($product_name) {
                if (!is_null($product_name) && $product_name!="") {
                    $subquery->where('products.name', 'ILIKE', '%' . $product_name . '%');
                }
            })
            ->where(function ($subquery) use ($date_start) {
                if (!is_null($date_start) && $date_start!="") {
                    $subquery->where('product_maintenances.done_date','>=',$date_start);
                }
            })
            ->where(function ($subquery) use ($date_end) {
                if (!is_null($date_end) && $date_end!="") {
                    $subquery->where('product_maintenances.done_date','<=',$date_end);
                }
            })
            ->where(function ($subquery) use ($product_serie) {
                if (!is_null($product_serie) && $product_serie!="") {
                    $subquery->where('product_series.serie','like','%'.$product_serie.'%');
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
