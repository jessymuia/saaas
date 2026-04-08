<?php

namespace App\Providers;

use App\Policies\AuditPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Gate;
use App\Models\InvoicePayment;
use App\Policies\InvoicePaymentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Audit::class => AuditPolicy::class,
                \App\Models\Invoice::class => \App\Policies\InvoicePolicy::class,
    \App\Models\InvoicePayment::class => \App\Policies\InvoicePaymentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->is_super_admin) {
                return true;
            }
        });
    }
}