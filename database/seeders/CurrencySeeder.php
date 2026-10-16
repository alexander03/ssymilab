<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::create([
            'name' => 'SOLES',
            'code' => 'PEN',
            'symbol' => 'S/',
            'base' => 'Y'
        ]);

        Currency::create([
            'name' => 'DÓLARES',
            'code' => 'USD',
            'symbol' => '$',
            'base' => 'N'
        ]);

        Currency::create([
            'name' => 'EUROS',
            'code' => 'EUR',
            'symbol' => '€',
            'base' => 'N'
        ]);
    }
}
