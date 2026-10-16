<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'dni',
        'ruc',
        'name',
        'surname_father',
        'surname_mother',
        'business_name',
        'address',
        'email',
        'phone',
	    'code',
        'shipping_person',
        'shipping_person_dni',
        'supervisor_id', 
    ];

    public function roles(){
        return $this->belongsToMany(Role::class, 'person_roles', 'person_id', 'role_id')->orderBy('roles.name', 'ASC');
    }
    
    public function users(){
        return $this->hasMany(User::class);
    }

    public function products(){
        return $this->hasMany(Product::class, 'supplier_id')->orderBy('products.name', 'ASC');
    }

    public function getNameComplete(){
        return ($this->type == '1') ? trim($this->surname_father . " " . $this->surname_mother . " " . $this->name) : trim($this->business_name);
    }

    public function getDocumentNumber(){
        return ($this->type == '1') ? trim($this->dni) : trim($this->ruc);
    }

    public function scopewith_rol($query, $role){
        $query->join('person_roles', 'person_roles.person_id', '=', 'people.id')
            ->join('roles', 'person_roles.role_id', '=', 'roles.id')
            ->where('roles.code', $role)
            ->orderBy('people.surname_father', 'ASC')
            ->orderBy('people.surname_mother', 'ASC')
            ->orderBy('people.name', 'ASC')
            ->orderBy('people.business_name', 'ASC')
            ->select('people.*');
    }

    public function scopesearch($query, $name, $document_number, $role_id){

        $query = $query->join('person_roles', 'people.id', '=', 'person_roles.person_id')
        ->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('name', 'ILIKE', '%' . $name . '%')
                    ->orWhere('surname_father', 'ILIKE', '%' . $name . '%')
                    ->orWhere('surname_mother', 'ILIKE', '%' . $name . '%')
                    ->orWhere('business_name', 'ILIKE', '%' . $name . '%')
                    ->orWhere(DB::raw("CONCAT(name, ' ', surname_father, ' ', surname_mother)"), 'ILIKE', '%' . $name . '%')
                    ->orWhere(DB::raw("CONCAT(surname_father, ' ', surname_mother, ' ', name)"), 'ILIKE', '%' . $name . '%');
            }
        })->where(function ($subquery) use ($document_number) {
            if (!is_null($document_number)) {
                $subquery->where('dni', 'ILIKE', '%' . $document_number . '%')
                    ->orWhere('ruc', 'ILIKE', '%' . $document_number . '%');
            }
        })->where(function ($subquery) use ($role_id) {
            if (!is_null($role_id)) {
                $subquery->where('person_roles.role_id', '=', $role_id);
            }
        })
        ->distinct();

        return $query;

    }

    public function scopesearch_autocomplete($query, $name, $role_code,$role_codes=null){

        $query = $query->join('person_roles', 'people.id', '=', 'person_roles.person_id')
        ->join('roles', 'roles.id', '=', 'person_roles.role_id')
        ->where(function ($subquery) use ($name) {
            if (!is_null($name)) {
                $subquery->where('people.name', 'ILIKE', '%' . $name . '%')
                    ->orWhere('surname_father', 'ILIKE', '%' . $name . '%')
                    ->orWhere('surname_mother', 'ILIKE', '%' . $name . '%')
                    ->orWhere('business_name', 'ILIKE', '%' . $name . '%')
                    ->orWhere('dni', 'ILIKE', '%' . $name . '%')
                    ->orWhere('ruc', 'ILIKE', '%' . $name . '%')
                    ->orWhere(DB::raw("CONCAT(people.name, ' ', surname_father, ' ', surname_mother)"), 'ILIKE', '%' . $name . '%')
                    ->orWhere(DB::raw("CONCAT(surname_father, ' ', surname_mother, ' ', people.name)"), 'ILIKE', '%' . $name . '%');
            }
        })->where(function ($subquery) use ($role_code) {
            if (!is_null($role_code)) {
                $subquery->where('roles.code', '=', $role_code);
            }
        })->where(function ($subquery) use ($role_codes) {
            if (!is_null($role_codes)) {
                $subquery->whereIn('roles.code',$role_codes);
            }
        })
        ->distinct();

        return $query;

    }
}
