<?php

namespace App\Http\Traits\Store;

use App\Librerias\Libreria;
use App\Models\StoreDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait StoreDocumentTrait
{
    public function search_store_documents($date_start, $date_end, $document_type_id, $number, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $document_type_id = Libreria::getParam($document_type_id);
        $user = Auth::user();
        $list = StoreDocument::search($date_start, $date_end, $document_type_id, $number, null, $user->branch_office_id);;
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function getNextNumber($document_type_id) : string
    {
        $user = Auth::user();
        $requirement = StoreDocument::where('branch_office_id', $user->branch_office_id)
            ->where('document_type_id', $document_type_id)
            ->orderBy('date_time', 'DESC')->first();

        if($requirement){
            $number = (int) $requirement->number;
            return str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }
        return "00000001";
    }

}
