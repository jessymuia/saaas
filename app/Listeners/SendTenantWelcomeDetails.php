<?php

namespace App\Listeners;

use App\Events\TenantRegistered;
use App\Mail\WelcomeTenantMail;
use Illuminate\Support\Facades\Mail;

class SendTenantWelcomeDetails
{
    public function handle(TenantRegistered $event): void
    {
        // Send the email to the user (sharded in Citus)
        Mail::to($event->user->email)->send(new WelcomeTenantMail($event->user));
    }
}