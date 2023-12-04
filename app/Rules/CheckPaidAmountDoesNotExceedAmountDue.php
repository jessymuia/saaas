<?php

namespace App\Rules;

use App\Models\Invoice;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Ensure that the amount being paid for the given invoice does not exceed the amount due.
 * This factors in, the invoice total, the credit note total and the amount paid.
 */
class CheckPaidAmountDoesNotExceedAmountDue implements ValidationRule
{
    protected int $invoiceID;
    public function __construct(int $invoiceID) {
        $this->invoiceID = $invoiceID;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // get the invoice
        $invoice = Invoice::find($this->invoiceID);

        $invoiceTotalDue = $invoice->totalDue();
        // if the total due is a decimal value, round up
        if(is_float($invoiceTotalDue)){
            $invoiceTotalDue = ceil($invoice->totalDue());
        }
        // fail if the amount being paid exceeds the total due
        if($value > $invoiceTotalDue){
            $fail("The amount being paid exceeds the total due.");
        }
    }
}
