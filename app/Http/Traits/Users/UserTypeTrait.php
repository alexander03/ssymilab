<?php

namespace App\Http\Traits\Users;

use App\Librerias\Libreria;
use App\Models\UserType;

trait UserTypeTrait
{
    // use DynamicFilter;

    public function search_user_types($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = UserType::search($name);
        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
