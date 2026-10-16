<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\MenuOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Action::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->word(),
            'name' => implode("", $this->faker->words()),
            'description' => $this->faker->paragraph(),
            'route' => null,
            'menu_option_id' => MenuOption::all()->random(1)->first()->id,
        ];
    }
}
