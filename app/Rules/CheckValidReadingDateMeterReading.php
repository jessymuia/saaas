<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
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
        $meterReading = \App\Models\MeterReading::query()
            ->where('unit_id', $this->unitId)
            ->where('utility_id', $this->utilityId)
            ->orderBy('reading_date', 'desc')
            ->first();

        if ($meterReading != null && $meterReading->reading_date >= $value) {
            $fail('The reading date must be greater than the previous reading date.');
        }
    }
}
