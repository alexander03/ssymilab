<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class ScheduleFile extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasMediaTrait;

    protected $fillable = [
        'name',
        'schedule_id',
    ];


    public function schedule(){
        return $this->belongsTo(Schedule::class);
    }

    public function scopeSearch($query, $schedule_id){

        $query = $query->where(function ($subquery) use ($schedule_id) {
            if (!is_null($schedule_id)) {
                $subquery->where('schedule_id', '=', $schedule_id);
            }
        ;

        return $query;
    }

}
