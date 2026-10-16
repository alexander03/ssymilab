<?php

namespace Database\Seeders;

use App\Models\BranchOffice;
use App\Models\Person;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $person = Person::find(1);
        $user_type = UserType::find(1);
        $branch_office = BranchOffice::find(1);

        User::create([
            'username' => 'admin',
            'email' => $person->email,
            'password' => Hash::make("admin123"),
            'branch_office_id' => $branch_office->id,
            'person_id' => $person->id,
            'user_type_id' => $user_type->id,
            'status' => 'Y',
        ]);
    }
}
