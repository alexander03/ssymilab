<?php

namespace Database\Seeders;

use App\Models\TransferMethod;
use Illuminate\Database\Seeder;

class TransferMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransferMethod::create([
            'name' => 'BCP'
        ]);

        TransferMethod::create([
            'name' => 'BBVA'
        ]);

        TransferMethod::create([
            'name' => 'YAPE'
        ]);
    }
}
