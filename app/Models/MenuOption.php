<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'order',
        'link',
        'menu_category_id',
    ];

    public function menu_category(){
        return $this->belongsTo(MenuCategory::class);
    }

    public function user_types(){
        return $this->belongsToMany(UserType::class, 'permissions', 'menu_option_id', 'user_type_id');
    }

    public function actions(){
        return $this->hasMany(Action::class)->orderBy('actions.id', 'ASC');
    }

    public function scopeSearch($query, $name, $menu_category_id){

        $query = $query->join('menu_categories', 'menu_categories.id', 'menu_options.menu_category_id')
        ->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('menu_options.name', 'ILIKE', '%' . $name . '%');
            }
        })->where(function ($subquery) use ($menu_category_id) {
            if (!is_null($menu_category_id)) {
                $subquery->where('menu_options.menu_category_id', '=', $menu_category_id);
            }
        });

        return $query;
    }
}
