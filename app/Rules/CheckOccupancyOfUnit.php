<?php

namespace App\Rules;

use App\Models\TenancyAgreement;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckOccupancyOfUnit implements ValidationRule
{
    public $startDate;
    public $formOperation;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($startDate, $formOperation)
    {
        //
        $this->startDate = $startDate;
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
        // check if the unit is occupied
        TenancyAgreement::query()
            ->where('unit_id', '=', $value)
            ->where('status', '=', 1)
            ->where('archive', '=', 0)
            ->where(function ($query){
                $query->where('end_date','>=',$this->startDate)
                    ->orWhere('end_date',null);
            })
            ->count() > 0 && $this->formOperation !== 'edit'
            ? $fail(__('The unit is occupied.')) : null;

    }
}
