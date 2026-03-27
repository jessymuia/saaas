<?php

namespace App\Console\Commands;

use App\Mail\AmountEscalationNotificationEmail;
use App\Models\EscalationRatesAndAmountsLogs;
use App\Models\TenancyAgreement;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EscalateAmountsCommand extends Command
{
    protected $signature = 'app:escalate-amounts-command';

    protected $description = 'Escalate rent/lease amounts when escalation date is reached, scoped per tenant';

    public function handle(): void
    {
        $tenants = \App\Models\SaasClient::all();

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            try {
                $this->escalateForTenant($tenant);
            } catch (\Throwable $e) {
                Log::error("[EscalateAmountsCommand] Failed for tenant {$tenant->id}: {$e->getMessage()}", [
                    'tenant_id' => $tenant->id,
                    'trace'     => $e->getTraceAsString(),
                ]);
            } finally {
                tenancy()->end();
            }
        }

        $this->info('Escalation complete.');
    }

    protected function escalateForTenant(\App\Models\SaasClient $tenant): void
    {
        DB::transaction(function () use ($tenant) {
            TenancyAgreement::query()
                ->whereNotNull('escalation_rate')
                ->where('next_escalation_date', now()->format('Y-m-d'))
                ->where('status', 1)
                ->chunk(100, function ($tenancyAgreements) use ($tenant) {
                    foreach ($tenancyAgreements as $tenancyAgreement) {
                        $this->processAgreement($tenancyAgreement, $tenant);
                    }
                });
        });
    }

    protected function processAgreement(TenancyAgreement $tenancyAgreement, \App\Models\SaasClient $tenant): void
    {
        $newAmount = $tenancyAgreement->amount
            + ($tenancyAgreement->amount * ($tenancyAgreement->escalation_rate / 100));
        $newAmount = number_format($newAmount, 2, '.', '');

        $nextEscalationDate = Carbon::parse($tenancyAgreement->next_escalation_date)
            ->addMonths($tenancyAgreement->escalation_period_in_months);

        // Log the escalation before mutating
        EscalationRatesAndAmountsLogs::create([
            'tenancy_agreement_id' => $tenancyAgreement->id,
            'escalation_rate'      => $tenancyAgreement->escalation_rate,
            'previous_amount'      => $tenancyAgreement->amount,
            'new_amount'           => $newAmount,
            'escalation_date'      => $tenancyAgreement->next_escalation_date,
            'status'               => 1,
            'archive'              => 0,
            'created_by'           => 1,
        ]);

        // Apply new amount and next escalation date
        $tenancyAgreement->amount               = $newAmount;
        $tenancyAgreement->next_escalation_date = $nextEscalationDate;
        $tenancyAgreement->save();

        // Build email payload
        $emailData = [
            'tenantName'          => $tenancyAgreement->tenant->name,
            'escalationRate'      => $tenancyAgreement->escalation_rate,
            'newRentAmount'       => $newAmount,
            'unitName'            => $tenancyAgreement->unit->name,
            'propertyName'        => $tenancyAgreement->unit->property->name,
            'oldRentAmount'       => $tenancyAgreement->amount,
            'escalationStartDate' => $tenancyAgreement->next_escalation_date,
            'escalationEndDate'   => $nextEscalationDate,
        ];

        $recipientEmail = $tenancyAgreement->tenant->email;

        // Dispatch as queued closure — QueueTenancyBootstrapper carries tenant context
        dispatch(function () use ($recipientEmail, $emailData) {
            Mail::to($recipientEmail)
                ->send(new AmountEscalationNotificationEmail($emailData));
        })->onQueue('escalations');

        Log::info("[EscalateAmountsCommand] Escalated agreement {$tenancyAgreement->id} for tenant {$tenancyAgreement->saas_client_id}. New amount: {$newAmount}");
    }
}