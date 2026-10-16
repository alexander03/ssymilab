<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductMoveMaintenance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_maintenance_id',
        'product_maintenance_detail_id',
        'branch_office_id',
        'product_id',
        'lot_id',
        'product_serie_id',
        'quantity',
    ];

    public function product_maintenance(){
        return $this->belongsTo(ProductMaintenance::class);
    }

    public function product_maintenance_detail(){
        return $this->belongsTo(ProductMaintenanceDetail::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function branch_office(){
        return $this->belongsTo(BranchOffice::class);
    }

    public function lot(){
        return $this->belongsTo(Lot::class);
    }
    public function product_serie(){
        return $this->belongsTo(ProductSerie::class);
    }

    public function scopeSearch($query, $schedule_id){

        $query = $query->join('product_maintenances','product_maintenances.id','=','product_move_maintenances.product_maintenance_id')
            ->join('schedules','schedules.id','=','product_maintenances.schedule_id')
            ->join('product_series', 'product_series.id', '=', 'product_maintenances.product_serie_id')
            ->join('products', 'products.id', '=', 'product_series.product_id')
            ->join(DB::raw('people as person'),'person.id','=','schedules.customer_id')
            ->where(function ($subquery) use ($schedule_id) {
                if (!is_null($schedule_id)) {
                    $subquery->where('schedule_id', '=', $schedule_id);
                }
            })
            /*->where(function ($subquery) use ($product_name) {
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
            })*/;

        return $query;
    }
}
