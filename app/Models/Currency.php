<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Currency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'base',
    ];

    public function scopeorder_currency($query){
        return $query->orderBy(DB::raw("CASE currencies.base WHEN 'Y' THEN 1 ELSE 2 END"), 'ASC')
            ->orderBy('name', 'ASC');
    }

    public static function base(){
        return Currency::where('base', 'Y')->first();
    }
}
