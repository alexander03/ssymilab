<?php

namespace Database\Factories;

use App\Models\MenuCategory;
use App\Models\MenuOption;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MenuOption::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'order' => 1,
            'link' => $this->faker->word(),
            'menu_category_id' => MenuCategory::all()->random(1)->first()->id,
        ];
    }
}
