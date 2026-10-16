<?php

namespace App\Http\Traits\Security;

use App\Librerias\Libreria;
use App\Models\Action;

trait ActionTrait
{
    public function search_actions($name, $menu_option_id, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $menu_option_id = Libreria::getParam($menu_option_id);
        $list = Action::search($name, $menu_option_id)->select('actions.*');
        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);
        return $list;
    }
}
