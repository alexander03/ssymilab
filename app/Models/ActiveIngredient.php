<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActiveIngredient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected function products(){
        return $this->belongsToMany(Product::class, 'product_ingredients', 'active_ingredient_id', 'product_id')->orderBy('products.name');
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
