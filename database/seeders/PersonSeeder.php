<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Person::create([
            'type' => '1',
            'dni' => '79136078',
            'ruc' => null,
            'name' => 'Guillermo',
            'surname_father' => 'Espinoza',
            'surname_mother' => 'Torres',
            'business_name' => null,
            'address' => 'Huáscar #285',
            'email' => 'guiesto.07@hormail.com',
            'phone' => '981702686',
        ]);
    }
}
