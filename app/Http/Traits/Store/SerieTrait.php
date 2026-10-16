<?php

namespace App\Http\Traits\Store;

use App\Librerias\Libreria;
use App\Models\ProductSerie ;
use Illuminate\Support\Facades\Auth;

trait SerieTrait
{

    public function search_autocompleteSerie($product_id, $product_name, $serie, $status, $branch_office_id, $productserie='',$product_serie_id=null,$product_type=null)
    {
        $user = Auth::user();
        $list = ProductSerie::search($product_id, $product_name, $serie, $status, $branch_office_id, $productserie,$product_serie_id,$product_type)
                ->select('product_series.*')->limit(5)->get();

        return $list->map(function($lot){
            return [
                "id" => $lot->id,
                'product_name' => $lot->product->code." ".$lot->product->name." ".$lot->product->model,
                "serie" => $lot->serie,
            ];
        })->toArray();
    }

}
