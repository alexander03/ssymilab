<?php

namespace App\Http\Traits\Store;

use App\Librerias\Libreria;
use App\Models\Lot;
use Illuminate\Support\Facades\Auth;

trait LotTrait
{
    public function search_lots($product_id, $product_name, $lot, $status, $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $lot = Libreria::getParam($lot);
        $status = Libreria::getParam($status);
        $user = Auth::user();
        $list = Lot::search($product_id, $product_name, $lot, $status, $user->branch_office_id)
            ->select('lots.*', 'products.name as product_name','products.presentation','brands.name as brand_name');
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_lotsreport($product_id, $product_name, $lot, $status,$branch_office_id, $brand_id, $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $lot = Libreria::getParam($lot);
        $status = Libreria::getParam($status);
        $user = Auth::user();
        $list = Lot::search($product_id, $product_name, $lot, $status, $branch_office_id)
            ->select('lots.*', 'products.name as product_name','products.presentation');
        if($brand_id!="" && !is_null($brand_id)){
            $list = $list->where('products.brand_id','=',$brand_id);
        }
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_autocompleteLot($lot, $product_id,$status='')
    {
        $product_name="";
        $user = Auth::user();
        $list = Lot::where(function($sql) use($lot) {
                $sql->where('lot','ilike', '%'.$lot.'%')->orWhere('barcode','ilike','%'.$lot.'%');
            })
                ->where('product_id', $product_id)
                ->where('branch_office_id', $user->branch_office_id)
                ->where('status', '<>', 'A')     
                ->where('remain','>',0)    
                ->where(function($sql) use($status){
                    if($status!=""){
                        $sql = $sql->where('status','like',$status);
                    }
                })
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
    }

    public function search_autocompleteLotCreditNote($lot, $product_id)
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
                ->select('lots.*')->limit(10)->get();

        return $list->map(function($lot){
            return [
                "id" => $lot->id,
                'lot' => $lot->lot,
                "due_date" => $lot->due_date,
                "remain" => $lot->remain,
                "barcode" => $lot->barcode,
            ];
        })->toArray();
    }

}
