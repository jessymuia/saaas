<?php

namespace App\Console\Commands;

use App\Models\EscalationRatesAndAmountsLogs;
use App\Models\TenancyAgreement;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CorrectEscalation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:correct-escalation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correct escalation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // update the next escalation date for all tenancy agreements where escalation rate is not null
//        \DB::transaction(function () {
//            // verify that the records are 13 before updating
//            $countOfAgreements = TenancyAgreement::query()
//                ->whereNotNull('escalation_rate')
//                ->count();
//            $countOfAgreements == 11
//                ? TenancyAgreement::query()
//                    ->whereNotNull('escalation_rate')
//                    ->chunk(100, function ($tenancyAgreements) {
//                        foreach ($tenancyAgreements as $tenancyAgreement) {
//                            $nextEscalationDate = Carbon::parse($tenancyAgreement->start_date)->addMonths($tenancyAgreement->escalation_period_in_months);
//                            $tenancyAgreement->update(['next_escalation_date' => $nextEscalationDate]);
//                        }
//                    })
//                : $this->info("The number of records is $countOfAgreements");
//        });

        // check if the next escalation date is in the past or has passed and escalate the amount by given amount
        \DB::transaction(function () {
            TenancyAgreement::query()
                ->whereNotNull('escalation_rate')
                ->where('next_escalation_date', '<', now()->format('Y-m-d'))
                ->chunk(100, function ($tenancyAgreements) {
                    foreach ($tenancyAgreements as $tenancyAgreement) {
                        // calculate new amount
                        $newAmount = $tenancyAgreement->amount + ($tenancyAgreement->amount * ($tenancyAgreement->escalation_rate / 100));
//                        $nextEscalationDate = Carbon::parse($tenancyAgreement->next_escalation_date)->addMonths($tenancyAgreement->escalation_period_in_months);
                        // define the number of decimal places
                        $newAmount = number_format($newAmount, 2,'.','');
                        // capture the past amount and log it in escalation rates and amounts logs
                        EscalationRatesAndAmountsLogs::create(
                            [
                                'tenancy_agreement_id' => $tenancyAgreement->id,
                                'escalation_rate' => $tenancyAgreement->escalation_rate,
                                'previous_amount' => $tenancyAgreement->amount,
                                'new_amount' => $newAmount,
                                'escalation_date' => $tenancyAgreement->next_escalation_date,
                                'status' => 1,
                                'archive' => 0,
                            ]
                        );

                        // update the tenancy agreement with the new amount and next escalation date
                        $tenancyAgreement->update(['amount' => $newAmount]);
                    }
                });
        });
    }
}
