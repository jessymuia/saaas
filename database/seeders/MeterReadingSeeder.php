<?php

namespace Database\Seeders;

use App\Models\MeterReading;
use App\Models\PropertyUtility;
use App\Models\TenancyAgreement;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeterReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }

    public function generateUnitID()
    {
        $unitIdArray = Unit::query()
            ->select('id')
            ->orderBy('id','asc')
            ->get()->toArray();
        return $this->faker->numberBetween($unitIdArray[0]['id'], $unitIdArray[count($unitIdArray) - 1]['id']);
    }

    public function generateReadingDate($unitID)
    {
        // get the 27 date of each month between the start date and end date of the tenancy agreement
        // get the tenancy agreements for this unit
        $tenancyAgreements = TenancyAgreement::query()
            ->select('id','start_date','end_date')
            ->where('unit_id', '=', $unitID)
            ->where('status', '=', 1)
            ->where('archive', '=', 0)
            ->get();

        foreach ($tenancyAgreements as $tenancyAgreement){
            $startDate = $tenancyAgreement->start_date;
            $endDate = $tenancyAgreement->end_date;
            $start = strtotime($startDate);
            $end = strtotime($endDate);
            $dates = [];
            for ($i = $start; $i <= $end; $i = strtotime('+1 day', $i)) {
                if(date('d', $i) == 27){
                    $dates[] = date('Y-m-d', $i);

                    $currentReading = $this->faker->randomFloat(2, 100, 1000);
                }
            }
            if (empty($dates)){
                $dates[] = $end;
            }
        }


        // return the reading dates
        return $dates ?? [];
    }

    public function getCurrentReading()
    {

    }

    public function getPreviousReadings($readableUtilities,$unitId)
    {
        $utilityPreviousReadings = [];
        // loop through the various readable utilities getting the previous reading
        foreach ($readableUtilities as $readableUtility){
            // get the previous reading for this utility
            $previousReading = MeterReading::query()
                ->select('current_reading')
                ->where('utility_id', '=', $readableUtility)
                ->where('unit_id', '=', $unitId)
                ->orderBy('reading_date','desc')
                ->limit(1)
                ->get()->first()->current_reading;
            $utilityPreviousReadings[$readableUtility] = $previousReading ?? 0;
        }
        return $utilityPreviousReadings;
    }

    public function getReadableUtilities($unitId)
    {
        // get the utilities that are readable
        $propertyId = Unit::query()
            ->select('property_id')
            ->where('id', '=', $unitId)
            ->get()->first()->property_id;

        $utilities = PropertyUtility::query()
            ->select('utility_id')
            ->where('property_id', '=', $propertyId)
            ->where('status', '=', 1)
            ->get()->toArray();

        return $utilities;
    }
}
