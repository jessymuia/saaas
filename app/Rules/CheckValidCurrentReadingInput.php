<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckValidCurrentReadingInput implements ValidationRule
{
    private float $previousReading;
    public function __construct(
        float $previousReading
    ) {
        $this->previousReading = $previousReading;
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
        if ($value < $this->previousReading) {
            $fail("The current reading must be greater than the previous reading.");
        }
    }
}
