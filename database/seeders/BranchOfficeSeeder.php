<?php

namespace Database\Seeders;

use App\Models\BranchOffice;
use Illuminate\Database\Seeder;

class BranchOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BranchOffice::create([
            'name' => 'PRINCIPAL',
            'address' => mb_strtoupper('Cal. Nicolas la Torre Nro. 126'),
            'phone' => '999999999',
            'company_id' => 1,
            'department_id' => '14',
            'province_id' => '1401',
            'district_id' => '140101',
        ]);
    }
}
