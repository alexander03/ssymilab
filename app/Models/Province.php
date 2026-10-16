<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'department_id'
    ];

    public function department(){
        return $this->belongsTo(Department::class, 'department_id', 'code');
    }

    public function districts(){
        return $this->hasMany(District::class, 'province_id', 'code');
    }
}
