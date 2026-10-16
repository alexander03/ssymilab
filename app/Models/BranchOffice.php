<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchOffice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'company_id',
        'department_id',
        'province_id',
        'district_id',
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

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function scopeSearch($query, $name){

        $query = $query->join('departments', 'departments.code', 'branch_offices.department_id')
        ->join('provinces', 'provinces.code', 'branch_offices.province_id')
        ->join('districts', 'districts.code', 'branch_offices.district_id')
        ->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('name', 'ILIKE', '%' . $name . '%');
            }
        });
        return $query;
    }
}
