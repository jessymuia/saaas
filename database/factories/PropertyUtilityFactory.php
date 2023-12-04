<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\RefBillingType;
use App\Models\RefUtility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyUtility>
 */
class PropertyUtilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyIdArray = Property::query()
            ->select('id')
            ->orderBy('id','asc')
            ->get()->toArray();
        $utilityIdArray = RefUtility::query()
            ->select('id')
            ->orderBy('id','asc')
            ->get()->toArray();
        $billingTypeIdArray = RefBillingType::query()
            ->select('id')
            ->orderBy('id','asc')
            ->get()->toArray();
        return [
            'property_id' => $this->faker->numberBetween($propertyIdArray[0]['id'], $propertyIdArray[count($propertyIdArray) - 1]['id']),
            'utility_id' => $this->faker->numberBetween($utilityIdArray[0]['id'], $utilityIdArray[count($utilityIdArray) - 1]['id']),
            'rate_per_unit' => $this->faker->numberBetween(200, 1000),
            'billing_type_id' => $this->faker->numberBetween($billingTypeIdArray[0]['id'], $billingTypeIdArray[count($billingTypeIdArray) - 1]['id']),
            'created_by' => 1,
        ];
    }
}
