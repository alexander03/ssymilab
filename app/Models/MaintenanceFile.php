<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class MaintenanceFile extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = [
        'name',
        'description',
        'date',
        'status',
        'product_maintenance_id',
    ];


    public function product_maintenance(){
        return $this->belongsTo(ProductMaintenance::class);
    }

    public function scopeSearch($query, $product_maintenance_id, $status){

        $query = $query->where(function ($subquery) use ($product_maintenance_id) {
            if (!is_null($product_maintenance_id)) {
                $subquery->where('product_maintenance_id', '=', $product_maintenance_id);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('status', '=', $status);
            }
        });

        return $query;
    }

}
