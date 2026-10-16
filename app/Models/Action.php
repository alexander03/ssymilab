<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'route',
        'menu_option_id',
    ];

    public function menu_option(){
        return $this->belongsTo(MenuOption::class);
    }

    public function user_types(){
        return $this->belongsToMany(UserType::class, 'operations', 'action_id', 'user_type_id')->orderBy('user_types.name');
    }

    public function scopeSearch($query, $name, $menu_option_id){

        $query = $query->join('menu_options', 'menu_options.id', 'actions.menu_option_id')
        ->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('actions.name', 'ILIKE', '%' . $name . '%');
            }
        })->where(function ($subquery) use ($menu_option_id) {
            if (!is_null($menu_option_id)) {
                $subquery->where('actions.menu_option_id', '=', $menu_option_id);
            }
        });

        return $query;
    }
}
