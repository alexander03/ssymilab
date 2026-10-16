<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'code' => '01',
            'name' => 'Amazonas'
        ]);

        Department::create([
            'code' => '02',
            'name' => 'Áncash'
        ]);

        Department::create([
            'code' => '03',
            'name' => 'Apurímac'
        ]);

        Department::create([
            'code' => '04',
            'name' => 'Arequipa'
        ]);

        Department::create([
            'code' => '05',
            'name' => 'Ayacucho'
        ]);

        Department::create([
            'code' => '06',
            'name' => 'Cajamarca'
        ]);

        Department::create([
            'code' => '07',
            'name' => 'Callao'
        ]);

        Department::create([
            'code' => '08',
            'name' => 'Cusco'
        ]);

        Department::create([
            'code' => '09',
            'name' => 'Huancavelica'
        ]);

        Department::create([
            'code' => '10',
            'name' => 'Huánuco'
        ]);

        Department::create([
            'code' => '11',
            'name' => 'Ica'
        ]);

        Department::create([
            'code' => '12',
            'name' => 'Junín'
        ]);

        Department::create([
            'code' => '13',
            'name' => 'La Libertad'
        ]);

        Department::create([
            'code' => '14',
            'name' => 'Lambayeque'
        ]);

        Department::create([
            'code' => '15',
            'name' => 'Lima'
        ]);

        Department::create([
            'code' => '16',
            'name' => 'Loreto'
        ]);

        Department::create([
            'code' => '17',
            'name' => 'Madre de Dios'
        ]);

        Department::create([
            'code' => '18',
            'name' => 'Moquegua'
        ]);

        Department::create([
            'code' => '19',
            'name' => 'Pasco'
        ]);

        Department::create([
            'code' => '20',
            'name' => 'Piura'
        ]);

        Department::create([
            'code' => '21',
            'name' => 'Puno'
        ]);

        Department::create([
            'code' => '22',
            'name' => 'San Martín'
        ]);

        Department::create([
            'code' => '23',
            'name' => 'Tacna'
        ]);

        Department::create([
            'code' => '24',
            'name' => 'Tumbes'
        ]);

        Department::create([
            'code' => '25',
            'name' => 'Ucayali'
        ]);
    }
}
