<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Operation;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = Action::all();

        foreach ($actions as $key => $value) {
            Operation::create([
                'action_id' => $value->id,
                'user_type_id' => 1,
            ]);
        }
    }
}
