<?php

namespace App\Http\Traits\Users;

use App\Librerias\Libreria;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait UserTrait
{
    public function search_users($username, $branch_office_id, $user_type_id, $person_name, $sort, $direction, $rows)
    {
        $username = Libreria::getParam($username);
        $branch_office_id = Libreria::getParam($branch_office_id);
        $user_type_id = Libreria::getParam($user_type_id);
        $list = User::search($username, $branch_office_id, $user_type_id, $person_name);
        $list = $list->select(
                "users.*",
                DB::raw("(CASE WHEN people.type = '1' THEN CONCAT(people.surname_father,' ',people.surname_mother,' ', people.name) ELSE people.business_name END) AS person_name"),
                "branch_offices.name AS branch_office_name",
                "user_types.name AS user_type_name"
            )
            ->orderBy($sort, $direction)
            ->paginate($rows);
        return $list;

    }
}
