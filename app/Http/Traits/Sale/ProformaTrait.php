<?php

namespace App\Http\Traits\Sale;

use App\Librerias\Libreria;
use App\Models\Proforma;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ProformaTrait
{
    public function search_proformas($date_start, $date_end, $number, $supplier, $sort, $direction, $rows, $status=null)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $user = Auth::user();
        $list = Proforma::search($date_start, $date_end, $number, $supplier, $status, $user->branch_office_id)
        ->select(
            "documents.*",
            DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS client_name"),
            DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS client_document"),
        );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);
        return $list;
    }

    public function getNextNumber() : string
    {
        $user = Auth::user();
        $proforma = Proforma::where('branch_office_id', $user->branch_office_id)->orderBy('created_at', 'DESC')->first();

        if($proforma){
            $number = (int) $proforma->number;
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }
        return "00000001";
    }

    public function search_proforma_autocomplete($number)
    {
        $user = Auth::user();
        $documents = Proforma::search(null, null, $number, null, null, $user->branch_office_id)
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
