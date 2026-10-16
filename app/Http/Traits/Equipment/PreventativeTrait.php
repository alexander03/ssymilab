<?php

namespace App\Http\Traits\Equipment;

use App\Librerias\Libreria;
use App\Models\Maintenance;

trait PreventativeTrait
{

    public function search_maintenance($product_name, $sort, $direction, $rows)
    {
        $product_name = Libreria::getParam($product_name);
        $list = Maintenance::search($product_name);

        $list = $list->orderBy($sort, $direction)
            ->select('maintenances.*', 'products.name AS product_name','products.model','brands.name as brand_name', 'products.id AS product_id')
            ->paginate($rows);


        return $list;
    }
}
