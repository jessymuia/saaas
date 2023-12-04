<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => $this->faker->numberBetween(1, 10),
            'name' => $this->faker->regexify('[A-Za-z0-9]{3}'),
            'unit_type_id' => $this->faker->numberBetween(1, 2),
            'created_by' => 1,
        ];
    }
}
