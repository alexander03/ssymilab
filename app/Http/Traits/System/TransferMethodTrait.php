<?php

namespace App\Http\Traits\System;

use App\Librerias\Libreria;
use App\Models\TransferMethod;

trait TransferMethodTrait
{
    public function search_transfer_methods($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = TransferMethod::search($name);

        $list = $list->select('transfer_methods.*')
            ->orderBy($sort, $direction)
            ->paginate($rows);

        return $list;
    }
}
