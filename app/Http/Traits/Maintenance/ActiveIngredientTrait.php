<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\ActiveIngredient;

trait ActiveIngredientTrait
{
    // use DynamicFilter;

    public function search_active_ingredients($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = ActiveIngredient::search($name);

        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);


        return $list;
    }
}
