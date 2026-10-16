<?php

namespace App\Http\Traits\Store;

use App\Librerias\Libreria;
use App\Models\ProductSerie;
use Illuminate\Support\Facades\Auth;

trait ProductSerieTrait
{
    public function search_series($product_id, $product_name, $serie, $status, $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $serie = Libreria::getParam($serie);
        $status = Libreria::getParam($status);
        $user = Auth::user();
        $list = ProductSerie::search($product_id, $product_name, $serie, $status, $user->branch_office_id)
            ->select('product_series.*', 'products.name as product_name','products.model','products.code','brands.name as brand_name');
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

    public function search_seriesreport($product_id, $product_name, $serie, $status, $branch_office_id, $sort, $direction, $rows)
    {
        $product_id = Libreria::getParam($product_id);
        $product_name = Libreria::getParam($product_name);
        $serie = Libreria::getParam($serie);
        $status = Libreria::getParam($status);
        $user = Auth::user();
        $list = ProductSerie::search($product_id, $product_name, $serie, $status, $branch_office_id)
            ->select('product_series.*', 'products.name as product_name','products.model','products.code');
        $list = $list->orderBy($sort, $direction);
        $list = $list->paginate($rows);

        return $list;
    }

}
