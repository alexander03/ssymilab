<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ruc',
        'business_name',
        'trade_name',
        'address',
        'password',
        'authentication_mode',
        'username_sunat',
        'password_sunat',
        'department_id',
        'province_id',
        'district_id',
        'status',
    ];

    public function department(){
        return $this->belongsTo(Department::class, 'department_id', 'code');
    }

    public function province(){
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }

    public function district(){
        return $this->belongsTo(District::class, 'district_id', 'code');
    }

    public function branch_offices(){
        return $this->hasMany(BranchOffice::class)->orderBy('branch_offices.name');
    }
}
