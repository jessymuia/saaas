<?php

namespace App\Rules;

use App\Models\Property;
use App\Models\PropertyUtility;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckUniqueUtilityInProperty implements ValidationRule
{
    protected int $propertyId;
    public function __construct(int $propertyId) {
        $this->propertyId = $propertyId;
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
        PropertyUtility::query()
            ->where('property_id', $this->propertyId)
            ->where('utility_id', $value)->exists()
            ? $fail('The utility is already added to this property') : null;
    }
}
