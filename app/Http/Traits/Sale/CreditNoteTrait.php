<?php


namespace App\Http\Traits\Sale;

use App\Librerias\Libreria;
use App\Models\CreditNote;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait CreditNoteTrait
{
    public function search_creditnotes($date_start, $date_end, $document_type_id, $number, $supplier, $vendedor, $status, $supervisor, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $document_type_id = Libreria::getParam($document_type_id);
        $user = Auth::user();
        $list = CreditNote::search($date_start, $date_end, $document_type_id, $number, $supplier, $status, $user->branch_office_id, $vendedor, $supervisor)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
                DB::raw("(CASE WHEN person.type = '1' THEN CONCAT(person.surname_father,' ',person.surname_mother,' ', person.name) ELSE person.business_name END) AS person_name"),
                DB::raw("(CASE WHEN supervisor.type = '1' THEN CONCAT(supervisor.surname_father,' ',supervisor.surname_mother,' ', supervisor.name) ELSE supervisor.business_name END) AS supervisor_name"),
                    DB::raw("documents2.number as number_ref")
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_sale_autocomplete_creditnote($number)
    {
        $user = Auth::user();
        $documents = Sale::search(null, null, null, $number, null, null, $user->branch_office_id,null)
        ->where('documents.status', '<>', 'A')
        ->orderBy('documents.created_at', 'DESC')
        ->select('documents.*')->limit(5)->get();

        return $documents->map(function($document){
            return [
                "id" => $document->id,
                "number" => $document->number,
            ];
        })->toArray();
    }

    public function getNextNumberCreditNote($document_type_id) : string
    {
        $user = Auth::user();

        if($user->branch_office_id==2){//PRINCIPAL
            $serie = "03" . "-";
        }else{
            $serie = "02" . "-";
        }

        if($document_type_id == 12){
            $serie = "BC" . $serie;
        }elseif($document_type_id == 13){
            $serie = "FC" . $serie;
        }

        $sale = CreditNote::where('branch_office_id', $user->branch_office_id)
            ->where('document_type_id', 17)
            ->where('number', 'LIKE', $serie . "%")
            ->orderBy('date_time', 'DESC')->first();
        if($sale){
            $number = (int) substr($sale->number,5,8);
            return $serie . str_pad(($number + 1), 8, '0', STR_PAD_LEFT);
        }else{
            if($user->branch_office_id==3){
                if($document_type_id == 12){
                    return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
                }elseif($document_type_id == 13){
                    return $serie . str_pad((1 + 1), 8, '0', STR_PAD_LEFT);
                }
            }elseif($user->branch_office_id==1){
                if($document_type_id == 12){
                    return $serie . str_pad((0 + 1), 8, '0', STR_PAD_LEFT);
                }elseif($document_type_id == 13){
                    return $serie . str_pad((1 + 1), 8, '0', STR_PAD_LEFT);
                }
            }
        }

        return $serie . "00000001";
    }

}
