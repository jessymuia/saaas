<?php

namespace App\Rules;

use App\Models\TenancyAgreement;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckOccupancyOfUnit implements ValidationRule
{
    public $startDate;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($startDate)
    {
        //
        $this->startDate = $startDate;
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
        // check if the unit is occupied
        TenancyAgreement::query()
            ->where('unit_id', '=', $value)
            ->where('status', '=', 1)
            ->where('archive', '=', 0)
            ->where(function ($query){
                $query->where('end_date','>=',$this->startDate)
                    ->orWhere('end_date',null);
            })
            ->count() > 0
            ? $fail(__('The unit is occupied.')) : null;

    }
}
