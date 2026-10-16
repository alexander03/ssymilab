<?php

namespace App\Http\Traits\System;

use App\Librerias\Libreria;
use App\Models\Card;

trait CardTrait
{
    public function search_cards($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = Card::search($name);

        $list = $list->select('cards.*')
            ->orderBy($sort, $direction)
            ->paginate($rows);

        return $list;
    }
}
