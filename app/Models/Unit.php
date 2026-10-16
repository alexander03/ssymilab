<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code'
    ];

    public function product_units(){
        return $this->hasMany(ProductUnit::class);
    }

    public function products(){
        return $this->hasMany(Product::class, 'base_unit_id')->orderBy('products.name');
    }

    public function scopeSearch($query, $name){

        $query = $query->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('name', 'ILIKE', '%' . $name . '%');
            }
        });

        return $query;
    }
}
