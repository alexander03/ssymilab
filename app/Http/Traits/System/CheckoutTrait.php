<?php

namespace App\Http\Traits\System;

use App\Librerias\Libreria;
use App\Models\Checkout;
use Illuminate\Support\Facades\Auth;

trait CheckoutTrait
{
    public function search_checkouts($name, $sort, $direction, $rows)
    {
        $name = Libreria::getParam($name);
        $user = Auth::user();
        $list = Checkout::search($name, $user->branch_office_id);

        $list = $list->select('checkouts.*')
            ->orderBy($sort, $direction)
            ->paginate($rows);

        return $list;
    }
}
