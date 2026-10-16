<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function products() {
        return $this->hasMany(Product::class)->orderBy('products.name');
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
