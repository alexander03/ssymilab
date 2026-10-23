<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\Person;
use App\Models\District;
use Illuminate\Support\Facades\DB;

trait PersonTrait {

    public function search_people($name, $document_number, $role_id, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $document_number = Libreria::getParam($document_number);
        $role_id = Libreria::getParam($role_id);
        $list = Person::search($name, $document_number, $role_id)->select('people.*');
        switch ($sort) {
            case 'name_complete':
                $list = $list->orderBy('people.surname_father', $direction)
                    ->orderBy('people.surname_mother', $direction)
                    ->orderBy('people.name', $direction)
                    ->orderBy('people.business_name', $direction);
                break;
            case 'document_number':
                $list = $list->orderBy('people.dni', $direction)
                    ->orderBy('people.ruc', $direction);
                break;
            default:
                $list = $list->orderBy($sort, $direction);
                break;
        }
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_autocomplete($name, $role)
    {
        $people = Person::search_autocomplete($name, $role)
        ->orderBy('people.surname_father', 'ASC')
        ->orderBy('people.surname_mother', 'ASC')
        ->orderBy('people.name', 'ASC')
        ->orderBy('people.business_name', 'ASC')
        ->select('people.*')->limit(5)->get();

        return $people->map(function($person){
            return [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
        })->toArray();
    }

    public function search_autocomplete2($name, $role)
    {
        $people = Person::search_autocomplete($name, null, $role)
        ->orderBy('people.surname_father', 'ASC')
        ->orderBy('people.surname_mother', 'ASC')
        ->orderBy('people.name', 'ASC')
        ->orderBy('people.business_name', 'ASC')
        ->select('people.*')->limit(5)->get();

        return $people->map(function($person){
            return [
                "id" => $person->id,
                'type' => $person->type,
                "name_complete" => $person->getNameComplete(),
                "document_number" => $person->getDocumentNumber()
            ];
        })->toArray();
    }

    public function search_autocomplete_district($name)
    {
        $people = District::join('provinces','provinces.code','=','districts.province_id')
        ->join('departments','departments.code','=','districts.department_id')
        ->where(DB::raw("concat(departments.name,' - ',provinces.name,' - ',districts.name)"),'ilike','%'.$name.'%')
        ->orderBy('departments.name', 'ASC')
        ->orderBy('provinces.name', 'ASC')
        ->orderBy('districts.name', 'ASC')
        //MAXIMO 30 COINCIDENCIAS (LA LISTA MUESTRA 10 Y EL RESTO CON SCROLL)
        ->select('districts.code','provinces.name as province_name','departments.name as department_name','districts.name as district_name')->limit(30)->get();

        return $people->map(function($person){
            return [
                "id" => $person->id,
                'code' => $person->code,
                "name_complete" => $person->department_name.' - '.$person->province_name.' - '.$person->district_name,
            ];
        })->toArray();
    }
}
