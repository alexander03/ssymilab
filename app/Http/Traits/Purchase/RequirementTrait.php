<?php

namespace App\Http\Traits\Purchase;

use App\Librerias\Libreria;
use App\Models\PurchaseRequirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait RequirementTrait
{
    public function search_requirements($date_start, $date_end, $number, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        $list = PurchaseRequirement::search($date_start,  $date_end, $number, $supplier, $user->branch_office_id)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);


        return $list;
    }

    public function search_requirements_report($branch_office_id,$date_start, $date_end, $number, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        $list = PurchaseRequirement::search($date_start,  $date_end, $number, $supplier, $branch_office_id)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);


        return $list;
    }

    public function getNextNumber() : string
    {
        $user = Auth::user();
        $requirement = PurchaseRequirement::where('branch_office_id', $user->branch_office_id)->orderBy('date_time', 'DESC')->first();

        if($requirement){
            $number = (int) $requirement->number;
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }
        return "00000001";
    }

    public function search_requirement_autocomplete($number)
    {
        $user = Auth::user();
        $documents = PurchaseRequirement::search(null, null, $number, null, $user->branch_office_id)
        ->where('documents.status', 'P')
        ->orderBy('documents.date_time', 'DESC')
        ->select('documents.*')->limit(5)->get();

        return $documents->map(function($document){
            return [
                "id" => $document->id,
                "number" => $document->number,
            ];
        })->toArray();
    }
}
