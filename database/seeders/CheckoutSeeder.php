<?php

namespace Database\Seeders;

use App\Models\Checkout;
use Illuminate\Database\Seeder;

class CheckoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Checkout::create([
            'name' => 'CAJA 1',
            'serie' => '001',
            'serie_note' => '01',
            'status' => 'C',
            'branch_office_id' => 1,
            'user_id' => null,
        ]);
    }
}
