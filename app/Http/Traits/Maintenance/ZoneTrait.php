<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\Zone;

trait ZoneTrait
{
    public function search_zones($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = Zone::search($name);

        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
