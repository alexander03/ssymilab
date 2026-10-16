<?php

namespace App\Http\Traits\Purchase;

use App\Librerias\Libreria;
use App\Models\EntryGuide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait EntryGuideTrait
{
    public function search_entry_guides($date_start, $date_end, $document_type_id, $number, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $document_type_id = Libreria::getParam($document_type_id);
        $user = Auth::user();
        $list = EntryGuide::search($date_start, $date_end, $document_type_id, $number, $supplier, null, $user->branch_office_id)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

}
