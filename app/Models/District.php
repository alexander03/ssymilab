<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'department_id',
        'province_id'
    ];

    public function department(){
        return $this->belongsTo(Department::class, 'department_id', 'code');
    }

    public function province(){
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }
}
