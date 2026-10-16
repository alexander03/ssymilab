<?php

namespace Database\Seeders;

use App\Models\MenuOption;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $menu_options = MenuOption::all();

        foreach ($menu_options as $key => $value) {
            Permission::create([
                'menu_option_id' => $value->id,
                'user_type_id' => 1,
            ]);
        }

    }
}
