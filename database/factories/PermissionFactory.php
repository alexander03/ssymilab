<?php

namespace Database\Factories;

use App\Models\MenuOption;
use App\Models\Permission;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'menu_option_id' => MenuOption::all()->random(1)->first()->id,
            'user_type_id' => UserType::all()->random(1)->first()->id,
        ];
    }
}
