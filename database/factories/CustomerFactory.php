<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'mobile' => '01' . $this->faker->numerify('#########'),
            'email' => $this->faker->boolean(50) ? $this->faker->unique()->safeEmail() : null,
            'address' => $this->faker->boolean(50) ? $this->faker->text(30) : null,
            'initial_balance' => rand(-50000, 50000),
            'added_by_id' => 1
        ];
    }
}
