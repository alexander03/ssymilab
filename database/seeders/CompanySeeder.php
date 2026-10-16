<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'ruc' => '20602871119',
            'business_name' => 'GARZASOFT E.I.R.L.',
            'trade_name' => 'GARZASOFT E.I.R.L.',
            'address' => mb_strtoupper('Cal. Nicolas la Torre Nro. 126'),
            'password' => null,
            'authentication_mode' => null,
            'username_sunat' => null,
            'password_sunat' => null,
            'department_id' => '14',
            'province_id' => '1401',
            'district_id' => '140101',
            'status' => 'Y',
        ]);
    }
}
