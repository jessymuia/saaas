<?php

namespace App\Listeners;

use App\Events\TenancyAgreementCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateDepositAmountInvoiceListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TenancyAgreementCreatedEvent $event): void
    {
        //
        // log
        \Log::info("Listener reached");
        $tenancyAgreement = $event->tenancyAgreement;

        $retuned = $tenancyAgreement->createDepositInvoice();
        \Log::info("Returned value: " . $retuned);
    }
}
