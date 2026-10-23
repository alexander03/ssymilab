<?php

namespace App\Http\Traits\Purchase;

use App\Librerias\Libreria;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait PurchaseTrait
{
    public function search_purchases($date_start, $date_end, $document_type_id, $number, $supplier, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $document_type_id = Libreria::getParam($document_type_id);
        $user = Auth::user();
        $list = Purchase::search($date_start, $date_end, $document_type_id, $number, $supplier, null, $user->branch_office_id)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);


        return $list;
    }

    public function search_purchase_autocomplete($number)
    {
        $user = Auth::user();
        $documents = Purchase::search(null, null, null, $number, null, null, $user->branch_office_id)
        ->where('documents.status', '<>', 'A')
        ->where('documents.dispatched', 'N')
        ->orderBy('documents.created_at', 'DESC')
        ->select('documents.*')->limit(5)->get();

        return $documents->map(function($document){
            return [
                "id" => $document->id,
                "number" => $document->number,
            ];
        })->toArray();
    }


    //TIPO DE COMPRA: "" = TODAS, "N" = NACIONAL (SIN DUA, CON O SIN GUIA DE ENTRADA), "E" = EXTRANJERO (CON DUA)
    public function filter_purchase_origin($query, $origin)
    {
        if($origin != "N" && $origin != "E"){
            return $query;
        }
        $dua = function ($subquery) {
            $subquery->select(DB::raw(1))
                ->from('documents as dua')
                ->whereColumn('dua.document_id', 'documents.id')
                ->where('dua.document_type_id', 5)
                ->where('dua.status', '<>', 'A')
                ->whereNull('dua.deleted_at');
        };
        return ($origin == "E") ? $query->whereExists($dua) : $query->whereNotExists($dua);
    }

    public function search_purchases_report($branch_office_id,$date_start, $date_end, $document_type_id, $number, $supplier, $sort, $direction, $rows, $origin = "")
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $document_type_id = Libreria::getParam($document_type_id);
        $list = Purchase::search($date_start, $date_end, $document_type_id, $number, $supplier, null, $branch_office_id)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
            );
        $list = $this->filter_purchase_origin($list, $origin);
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);


        return $list;
    }

    public function search_purchases_product_report($branch_office_id,$date_start, $date_end, $document_type_id, $number, $supplier, $product, $sort, $direction, $rows)
    {
        $date_start = Libreria::getParam($date_start);
        $date_end = Libreria::getParam($date_end);
        $number = Libreria::getParam($number);
        $supplier = Libreria::getParam($supplier);
        $document_type_id = Libreria::getParam($document_type_id);
        $list = Purchase::SearchDetail($date_start, $date_end, $document_type_id, $number, $supplier, $product, null, $branch_office_id)
            ->select(
                "documents.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS supplier_name"),
                DB::raw("(CASE WHEN people.type = '1' THEN people.dni ELSE people.ruc END) AS supplier_document"),
                DB::raw('products.name as product_name'),'document_details.purchase_price','document_details.quantity','products.model','products.presentation'
            );
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);


        return $list;
    }
}
