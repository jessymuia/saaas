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
    protected string $formOperation;

    public function __construct(int $propertyId, string $formOperation) {
        $this->propertyId = $propertyId;
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
        if ($this->formOperation != 'edit'){
            PropertyUtility::query()
                ->where('property_id', $this->propertyId)
                ->where('utility_id', $value)->exists()
                ? $fail('The utility is already added to this property') : null;
        }
    }
}
