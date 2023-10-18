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

    public function __construct(
        $unitId,
        $utilityId,
    ) {
        $this->unitId = $unitId;
        $this->utilityId = $utilityId;
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

        $meterReadingExists = \App\Models\MeterReading::query()
            ->where('unit_id', $this->unitId)
            ->where('utility_id', $this->utilityId)
            ->whereMonth('reading_date', Carbon::createFromFormat('Y-m-d', $value)->format('m'))
            ->exists();

        if ($meterReadingExists) {
            $fail('The reading date must be greater than the previous reading month.');
        }
    }
}
