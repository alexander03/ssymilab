<?php

namespace Database\Factories;

use App\Models\BranchOffice;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchOfficeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BranchOffice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'company_id' => 1,
            'department_id' => '14',
            'province_id' => '1401',
            'district_id' => '140101',
        ];
    }
}
