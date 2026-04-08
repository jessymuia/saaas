<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckValidReadingDateMeterReading implements ValidationRule
{
    public int $unitId;
    public int $utilityId;

    public string $formOperation;

    public function __construct(
        $unitId,
        $utilityId,
        $formOperation
    ) {
        $this->unitId = $unitId;
        $this->utilityId = $utilityId;
        $this->formOperation = $formOperation;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        Log::info("Value: " . $value);

        if ($this->formOperation !== 'edit') {
            // check the latest meter reading date
            $latestMeterReading = \App\Models\MeterReading::query()
                ->where('unit_id', $this->unitId)
                ->where('utility_id', $this->utilityId)
                ->orderBy('reading_date', 'desc')
                ->first();
            if ($latestMeterReading != null && $latestMeterReading->current_reading != 0) {

                $meterReadingExists = \App\Models\MeterReading::query()
                    ->where('unit_id', $this->unitId)
                    ->where('utility_id', $this->utilityId)
                    ->whereMonth('reading_date', Carbon::createFromFormat('Y-m-d', $value)->format('m'))
                    ->whereYear('reading_date', Carbon::createFromFormat('Y-m-d', $value)->format('Y'))
                    ->exists();

                if ($meterReadingExists) {
                    $fail('The reading date must be greater than the previous reading month.');
                }
            }
        }
    }
}
