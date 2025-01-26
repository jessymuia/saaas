<?php

namespace App\Console\Commands;

use App\Mail\AmountEscalationNotificationEmail;
use App\Models\EscalationRatesAndAmountsLogs;
use App\Models\Invoice;
use App\Models\TenancyAgreement;
use App\Models\TenancyBill;
use App\Utils\AppUtils;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class EscalateAmountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:escalate-amounts-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to escalate the rent/lease amount once the escalation date reaches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // create transaction
        // get all tenancy agreements that are active and have escalation date today
//        \DB::transaction(function () {
//            TenancyAgreement::query()
//                ->whereNotNull('escalation_rate')
//                ->where('next_escalation_date', now()->format('Y-m-d'))
//                ->where('status', 1)
//                ->chunk(100, function ($tenancyAgreements) {
//                    foreach ($tenancyAgreements as $tenancyAgreement) {
//                        // for each tenancy agreement, create an invoice that bills up to the present day
////                        $this->createInvoiceAndTenancyBill($tenancyAgreement);, check the escalation logs when invoicing normally
//                        // calculate new amount
//                        $newAmount = $tenancyAgreement->amount + ($tenancyAgreement->amount * ($tenancyAgreement->escalation_rate / 100));
//                        $nextEscalationDate = Carbon::parse($tenancyAgreement->next_escalation_date)->addMonths($tenancyAgreement->escalation_period_in_months);
//
//                        $newAmount = number_format($newAmount, 2, '.', '');
//
//                        // capture the past amount and log it in escalation rates and amounts logs
//                        EscalationRatesAndAmountsLogs::create(
//                            [
//                                'tenancy_agreement_id' => $tenancyAgreement->id,
//                                'escalation_rate' => $tenancyAgreement->escalation_rate,
//                                'previous_amount' => $tenancyAgreement->amount,
//                                'new_amount' => $newAmount,
//                                'escalation_date' => $tenancyAgreement->next_escalation_date,
//                                'status' => 1,
//                                'archive' => 0,
//                                'created_by' => 1,//TODO: Change value to SYSTEM USER
//                            ]
//                        );
//
//                        // capture the email data
//                        $emailData = [
//                            'tenantName' => $tenancyAgreement->tenant->name,
//                            'escalationRate' => $tenancyAgreement->escalation_rate,
//                            'newRentAmount' => $newAmount,
//                            'unitName' => $tenancyAgreement->unit->name,
//                            'propertyName' => $tenancyAgreement->unit->property->name,
//                            'oldRentAmount' => $tenancyAgreement->amount,
//                            'escalationStartDate' => $tenancyAgreement->next_escalation_date,
//                            'escalationEndDate' => $nextEscalationDate,
//                        ];
//
//                        // calculate the new amount and next escalation date
//                        $tenancyAgreement->amount = $newAmount;
//                        $tenancyAgreement->next_escalation_date = $nextEscalationDate;
//
//                        // store the new amount in the tenancy agreement
//                        $tenancyAgreement->save();
//
//                        // dispatch job to send out email to the tenant
//                        dispatch(function () use($tenancyAgreement,$emailData){
////                            Mail::to($tenancyAgreement->tenant->email)
//                            Mail::to('dundafuta@gmail.com')
//                                ->send(new AmountEscalationNotificationEmail($emailData));
//                        });
//                    }
//                });
//        });
    }

    public function createInvoiceAndTenancyBill($tenancyAgreement): void
    {
//        // create invoice if not exists
//        $invoice = new Invoice();
//
//        $invoice->tenancy_agreement_id = $tenancyAgreement->id;
//        $invoice->invoice_for_month = now()->format('Y-m-d');
//        $invoice->invoice_due_date = Carbon::now()->addMonth()->format('Y-m-5'); // set it to the 5th of next month
//
//        $invoice->save();
//
//        // create a tenancy bill that calculates the amount to be billed up to given day
//        $tenancyBill = new TenancyBill();
//
//        $tenancyBill->tenancy_agreement_id = $tenancyAgreement->id;
//        $tenancyBill->name = $tenancyAgreement->tenant->name . ' ' . now()->format('F') . ' Rent Bill';
//        $tenancyBill->bill_date = $tenancyAgreement->next_escalation_date;
//        $tenancyBill->due_date = Carbon::now()->addMonth()->format('Y-m-5'); // set it to the 5th of next month
//        $rentAmountWithoutTax = (Carbon::parse($tenancyAgreement->next_escalation_date)->diffInDays(now()->firstOfMonth()) / now()->daysInMonth) * $tenancyAgreement->amount;
//        $vatAmount = $rentAmountWithoutTax * AppUtils::VAT_RATE;
//        $tenancyBill->amount = $rentAmountWithoutTax;
//        $tenancyBill->vat = $vatAmount;
//        $tenancyBill->total_amount = $rentAmountWithoutTax + $vatAmount;
//        $tenancyBill->billing_type_id = $tenancyAgreement->billing_type_id;
//        $tenancyBill->invoice_id = $invoice->id;
//        $tenancyBill->created_by = 1;
//
//        $tenancyBill->save();
    }
}
