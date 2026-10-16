<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
    ];

    public function provinces(){
        return $this->hasMany(Province::class, 'department_id', 'code');
    }

    public function districts(){
        return $this->hasMany(District::class, 'department_id', 'code');
    }
}
