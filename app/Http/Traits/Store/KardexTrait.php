<?php

namespace App\Http\Traits\Store;

use App\Librerias\Libreria;
use App\Models\Kardex;
use Illuminate\Support\Facades\Auth;

trait KardexTrait
{
    public function search_kardex($product_id, $product_name, $fechainicio, $fechafin,  $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $fechainicio = Libreria::getParam($fechainicio);
        $fechafin = Libreria::getParam($fechafin);
        $user = Auth::user();
        $list = Kardex::search($product_id, $product_name, $fechainicio, $fechafin, $user->branch_office_id)
            ->select('kardexes.*', 'products.name as product_name','documents.date_time','documents.number','people.business_name','people.name','people.surname_father','people.surname_mother','brands.name as brand_name','products.presentation')
            ;
        $list = $list->orderBy($sort, $direction)->orderBy('documents.date_time','asc');
        $list = $list->paginate($rows);

        return $list;
    }

    /*public function search_autocompleteLot($lot, $product_id)
    {
        $product_name="";
        $status = "";
        $user = Auth::user();
        $list = Lot::where(function($sql) use($lot) {
                $sql->where('lot','ilike', '%'.$lot.'%')->orWhere('barcode','ilike','%'.$lot.'%');
            })
                ->where('product_id', $product_id)
                ->where('branch_office_id', $user->branch_office_id)
                ->where('status', '<>', 'A')         
                ->select('lots.*')->limit(5)->get();

        return $list->map(function($lot){
            return [
                "id" => $lot->id,
                'lot' => $lot->lot,
                "due_date" => $lot->due_date,
                "remain" => $lot->remain,
                "barcode" => $lot->barcode,
            ];
        })->toArray();
    }*/

}
