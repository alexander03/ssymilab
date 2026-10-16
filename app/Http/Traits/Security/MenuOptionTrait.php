<?php

namespace App\Http\Traits\Security;

use App\Librerias\Libreria;
use App\Models\MenuOption;

trait MenuOptionTrait
{
    public function search_menu_options($name, $menu_category_id, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $menu_category_id = Libreria::getParam($menu_category_id);
        $list = MenuOption::search($name, $menu_category_id)->select('menu_options.*');
        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);
        return $list;
    }
}
