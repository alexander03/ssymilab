<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = (rand(1, 2) == 1) ? "1" : "6";
        return [
            'type' => $type,
            'dni' => ($type == "1") ? Str::random(8) : null,
            'ruc' => ($type == "6") ? Str::random(11) : null,
            'name' => ($type == "1") ? $this->faker->name() : null,
            'surname_father' => ($type == "1") ? $this->faker->lastName() : null,
            'surname_mother' => ($type == "1") ? $this->faker->lastName() : null,
            'business_name' => ($type == "6") ? $this->faker->company() : null,
            'address' => $this->faker->address(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
