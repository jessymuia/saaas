<?php

namespace Database\Seeders;

use App\Models\MeterReading;
use App\Models\PropertyUtility;
use App\Models\TenancyAgreement;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class MeterReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->generateReadingsForExistingTenancyAgreements();
    }

    public function generateReadingsForExistingTenancyAgreements(): void
    {
        $tenancyAgreements = TenancyAgreement::query()
            ->select('id','start_date','end_date','unit_id')
            ->where('status', '=', 1)
            ->where('archive', '=', 0)
            ->get();

        foreach ($tenancyAgreements as $tenancyAgreement){
            $startDate = $tenancyAgreement->start_date;
            $endDate = $tenancyAgreement->end_date;
            $start = strtotime($startDate);
            $end = $endDate > date('Y-m-d')
                ? strtotime(date('Y-m-d'))
                : strtotime($endDate); // this check ensures the end date is not a future date
            $readingDatesAndConsumptionArray = [];
            for ($i = $start; $i <= $end; $i = strtotime('+1 day', $i)) {
                if(date('d', $i) == 27){
                    $readingDatesAndConsumptionArray[] = [
                        'date' => date('Y-m-d', $i),
                        'consumption' => rand(3, 15)
                    ];
                }
            }
            if (empty($readingDatesAndConsumptionArray)){
                $readingDatesAndConsumptionArray[] = [
                    'date' => $end,
                    'consumption' => rand(3, 15)
                ];
            }

            // compare the end date and reading date and see whether the reading date is in the same month as the end date
            $endMonth = date('m', $end);
            $readingDateMonth = date('m', strtotime($readingDatesAndConsumptionArray[count($readingDatesAndConsumptionArray) - 1]['date']));
            // check if the end date and reading date are in the same month and if the reading is in a future date for a continuing engagement
            if ($endMonth >= $readingDateMonth && strtotime($endDate) == $end){
                // if not, then add the end date to the reading dates and consumption array
                $readingDatesAndConsumptionArray[] = [
                    'date' => $endDate,
                    'consumption' => rand(3, 15)
                ];
            }

            foreach ($readingDatesAndConsumptionArray as $key => $dateAndConsumption){
                $unitId = $tenancyAgreement->unit_id;
                // get the readable utilities for this unit
                $readableUtilities = $this->getReadableUtilities($unitId);
                // get the previous readings for this unit
                $previousReadings = $this->getPreviousReadings($readableUtilities,$unitId);
                // loop through the readable utilities
                foreach ($readableUtilities as $readableUtility){
                    // get the current reading
                    $currentReading = $previousReadings[$readableUtility] + $dateAndConsumption['consumption'];

                    // create the meter reading
                    $meterReading = MeterReading::query()->create([
                        'unit_id' => $unitId,
                        'utility_id' => $readableUtility,
                        'reading_date' => $dateAndConsumption['date'],
                        'current_reading' => $currentReading,
                        'previous_reading' => $previousReadings[$readableUtility],
                        'consumption' => $dateAndConsumption['consumption'],
                        'has_bill' => 0,
                        'created_by' => 1,
                        'status' => 1,
                        'archive' => 0
                    ]);
                    // update the previous reading for this utility
                    $previousReadings[$readableUtility] = $currentReading;
                }
            }
        }
    }

    public function getPreviousReadings($readableUtilities,$unitId): array
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
                ->get()->first()->current_reading ?? 0;
            Log::info('Previous reading for utility '.$readableUtility.' is '.$previousReading);
            $utilityPreviousReadings[$readableUtility] = $previousReading ?? 0;
        }
        return $utilityPreviousReadings;
    }

    public function getReadableUtilities($unitId): array
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
            ->pluck('utility_id')->toArray();

        return $utilities;
    }
}
