<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductMaintenanceDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_maintenance_id',
        'product_id',
        'unit_id',
        'quantity',
        'guia',
    ];

    public function productmaintenace(){
        return $this->belongsTo(ProductMaintenance::class);
    }

}
