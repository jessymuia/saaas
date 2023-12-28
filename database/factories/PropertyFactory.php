<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyTypeId = $this->faker->numberBetween(1, 2);

        return [
            //
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'description' => $this->faker->text(),
            'property_type_id' => $propertyTypeId,
            'number_of_units' => $this->faker->numberBetween(20,80),
            'is_vatable' => $propertyTypeId === 1,
            'created_by' => 1,
        ];
    }
}
