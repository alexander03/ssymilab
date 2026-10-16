<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Operation;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Operation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'action_id' => Action::all()->random(1)->first()->id,
            'user_type_id' => UserType::all()->random(1)->first()->id,
        ];
    }
}
