<?php

namespace Database\Factories;

use App\Models\Property;
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
        $propertyIdArray = Property::query()->select('id')->orderBy('id','asc')->get()->toArray();
        do{
            $propertyID = $this->faker->numberBetween($propertyIdArray[0]['id'], $propertyIdArray[count($propertyIdArray) - 1]['id']);

            // check if property has available units
            $property = Property::find($propertyID);
        }while($property->units()->count() >= $property->number_of_units);

        // check if the unit is unique
        do{
            $unitName = $this->faker->regexify('[A-Za-z0-9]{3}');
        }while($property->units()->where('name', $unitName)->count() > 0);

        return [
            'property_id' => $propertyID,
            'name' => $unitName,
            'unit_type_id' => $this->faker->numberBetween(1, 2),
            'created_by' => 1,
        ];
    }
}
