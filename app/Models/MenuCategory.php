<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'order',
        'icon',
    ];

    public function menu_options(){
        return $this->hasMany(MenuOption::class)->orderBy('menu_options.name', 'ASC');
    }

    public function scopeSearch($query, $general_search, $array_filter = []){

        $query = $query->where(function ($subquery) use ($general_search) {
            if (!is_null($general_search)) {
                $subquery->where('name', 'ILIKE', '%' . $general_search . '%');
            }
        });

        return $query;
    }
}
