<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
    ];

    public function people(){
        return $this->belongsToMany(Person::class, 'person_roles', 'role_id', 'person_id')
            ->orderBy('people.surname_father', 'ASC')
            ->orderBy('people.surname_mother', 'ASC')
            ->orderBy('people.name', 'ASC')
            ->orderBy('people.business_name', 'ASC');
    }
}
