<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function menu_options(){
        return $this->belongsToMany(MenuOption::class, 'permissions', 'user_type_id', 'menu_option_id')->orderBy('menu_options.name', 'ASC');
    }

    public function actions(){
        return $this->belongsToMany(Action::class, 'operations', 'user_type_id', 'action_id')->orderBy('actions.name', 'ASC');
    }

    public function scopeactions_by_menu_option($query, int $menu_option_id){
        return $this->belongsToMany(Action::class, 'operations', 'user_type_id', 'action_id')
            ->where('actions.menu_option_id', $menu_option_id)
            ->orderBy('actions.name', 'ASC');
    }

    public function scopesearch($query, $name){

        $query = $query->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('name', 'ILIKE', '%' . $name . '%');
            }
        });

        return $query;

    }
}
