<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'maintenance_id',
        'name',
        'type',
    ];

    public function maintenance(){
        return $this->belongsTo(Maintenance::class);
    }
}
