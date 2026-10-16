<?php

namespace App\Http\Traits\Security;

use App\Librerias\Libreria;
use App\Models\MenuCategory;

trait MenuCategoryTrait
{
    // use DynamicFilter;

    public function search_menu_categories($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = MenuCategory::search($name);

        // $list = $this->filter_by_array($list, $array_filter);

        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
