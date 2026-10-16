<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'status',
        'message',
        'date_time_created',
        'date_time_seen',
        'register_id',
        'register_type'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function scopeSearch($query, $user_id, $date_time_created, $status){

        $query = $query->join('users', 'users.id', '=', 'notifications.user_id')
        ->where(function ($subquery) use ($user_id) {
            if (!is_null($user_id)) {
                $subquery->where('users.id', '=', $user_id);
            }
        })->where(function ($subquery) use ($date_time_created) {
            if (!is_null($date_time_created)) {
                $subquery->where('date_time_created', '>=', $date_time_created);
            }
        })->where(function ($subquery) use ($status) {
            if (!is_null($status)) {
                $subquery->where('notifications.status', '=', $status);
            }
        })->orderBy('notifications.date_time_created', 'DESC');

        return $query;
    }
}
