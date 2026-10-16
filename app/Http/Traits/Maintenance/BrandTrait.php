<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\Brand;

trait BrandTrait
{
    // use DynamicFilter;

    public function search_brands($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = Brand::search($name);

        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
