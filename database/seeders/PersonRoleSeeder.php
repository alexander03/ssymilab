<?php

namespace Database\Seeders;

use App\Models\PersonRole;
use Illuminate\Database\Seeder;

class PersonRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PersonRole::create([
            'role_id' => 3,
            'person_id' => 1,
        ]);
    }
}
