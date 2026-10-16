<?php

namespace App\Http\Traits\System;

use App\Librerias\Libreria;
use App\Models\BranchOffice;

trait BranchOfficeTrait
{
    public function search_branch_offices($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $list = BranchOffice::search($name);

        $list = $list->select('branch_offices.*')
            ->orderBy($sort, $direction)
            ->paginate($rows);

        return $list;
    }
}
