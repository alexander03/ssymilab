<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'person_id',
    ];
}
