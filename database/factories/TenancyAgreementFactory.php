<?php

namespace Database\Factories;

use App\Models\RefBillingType;
use App\Models\TenancyAgreement;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TenancyAgreement>
 */
class TenancyAgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->generateRandomStartDate();
        $endDate = $this->faker->dateTimeBetween($startDate, rand(2,16) . ' month')->format('Y-m-d');
        $unitId = $this->selectUnit($startDate,$endDate);
        $tenantId = $this->generateRandomTenant();
        $billingTypeId = $this->generateBillingType();

        return [
            'unit_id' => $unitId,
            'tenant_id' => $tenantId,
            'agreement_type_id' => $this->faker->numberBetween(1, 2),
            'billing_type_id' => $billingTypeId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $this->faker->randomFloat(2, 3500, 100000),
            'escalation_rate' => $this->faker->randomFloat(2, 0, 50),
            'created_by' => 1,
        ];
    }

    public function generateRandomStartDate(): string
    {
        // generate  random date between 2 years ago and 8 months ago
        return $this->faker->dateTimeBetween('-2 years', '-8 months')->format('Y-m-d');
    }

    public function selectUnit($startDate,$endDate)
    {
        // select array of all available units
        $unitsIdArray = Unit::query()->select('id')->get()->toArray();
        do{
            // select random unit id from array
            $unitId = $this->faker->randomElement($unitsIdArray)['id'];

            // check if the unit is occupied
            $tenancyAgreement = TenancyAgreement::query()
                ->where('unit_id', '=', $unitId)
                ->where('status', '=', 1)
                ->where('archive', '=', 0)
                ->where(function ($query) use ($startDate){
                    $query->whereDate('end_date','>=',$startDate)
                        ->orWhere('end_date',null);
                })
                ->get();

            $tenancyAgreementCount = $tenancyAgreement->count();
        }while($tenancyAgreementCount > 0);
        return $unitId;
    }

    public function generateRandomTenant()
    {
        // select array of all available tenants
        $tenantsIdArray = Tenant::query()->select('id')->get()->toArray();
        // select random tenant id from array
        return $this->faker->randomElement($tenantsIdArray)['id'];
    }

    public function generateBillingType()
    {
        // generate array of all available billing types
        $billingTypesArray = RefBillingType::query()->select('id')->get()->toArray();
        // select random billing type id from array
        return $this->faker->randomElement($billingTypesArray)['id'];
    }
}
