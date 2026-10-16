<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\Category;

trait CategoryTrait
{
    // use DynamicFilter;

    public function search_categories($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = Category::search($name);

        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
