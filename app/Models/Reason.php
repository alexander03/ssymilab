<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reason extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'document_type_id',
        'name',
    ];

    public function module(){
        return $this->belongsTo(Module::class);
    }

    public function document_type(){
        return $this->belongsTo(DocumentType::class);
    }

    public function scopeSearch($query, $name, $module_id, $document_type_id){

        $query = $query->where(function ($subquery) use ($module_id) {
            if (!is_null($module_id)) {
                $subquery->where('reasons.module_id', '=', $module_id);
            }
        })->where(function ($subquery) use ($document_type_id) {
            if (!is_null($document_type_id)) {
                $subquery->where('reasons.document_type_id', '=', $document_type_id);
            }
        })->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('reasons.name', 'ilike', '%'.$name.'%');
            }
        });

        return $query;
    }
}
