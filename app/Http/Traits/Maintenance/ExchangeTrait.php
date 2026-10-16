<?php

namespace App\Http\Traits\Maintenance;

use App\Librerias\Libreria;
use App\Models\ExchangeRate;

trait ExchangeTrait
{
    // use DynamicFilter;

    public function search_exhange($date_start, $date_end, $sort, $direction, $rows)
    {
        $list = ExchangeRate::search($date_start,$date_end);

        $list = $list->orderBy($sort, $direction)
            ->paginate($rows);
        return $list;
    }
}
