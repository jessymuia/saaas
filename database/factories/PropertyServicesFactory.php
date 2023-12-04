<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyServices;
use App\Models\RefBillingType;
use App\Models\Services;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyServices>
 */
class PropertyServicesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do{
            $propertyIdArray = Property::query()
                ->select('id')
                ->orderBy('id','asc')
                ->get()->toArray();

            $propertyId = $this->faker->numberBetween($propertyIdArray[0]['id'], $propertyIdArray[count($propertyIdArray) - 1]['id']);

            $serviceIdArray = Services::query()
                ->select('id')
                ->orderBy('id','asc')
                ->get()->toArray();

            $serviceId = $this->faker->numberBetween($serviceIdArray[0]['id'], $serviceIdArray[count($serviceIdArray) - 1]['id']);
        }while(PropertyServices::query()
            ->where('property_id', '=', $propertyId)
            ->where('service_id', '=', $serviceId)
            ->count() > 0);

        // billing type id
        $billingTypeIdArray = RefBillingType::query()
            ->select('id')
            ->orderBy('id','asc')
            ->get()->toArray();

        return [
            'property_id' => $propertyId,
            'service_id' => $serviceId,
            'rate' => $this->faker->numberBetween(200, 1000),
            'billing_type_id' => $this->faker->numberBetween($billingTypeIdArray[0]['id'], $billingTypeIdArray[count($billingTypeIdArray) - 1]['id']),
            'created_by' => 1,
        ];
    }
}
