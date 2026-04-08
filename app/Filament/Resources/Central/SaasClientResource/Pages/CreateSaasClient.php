<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Events\TenantRegistered;
use App\Filament\Resources\Central\SaasClientResource;
use App\Models\Domain;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateSaasClient extends CreateRecord
{
    protected static string $resource = SaasClientResource::class;

    /**
     * Handle post-creation setup: domain record, subscription, usage metric, admin user.
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        if (!$record) {
            Log::error('SaasClient record not found after creation');
            return;
        }

        // The 'domain' field in the form is a virtual column stored in data['domain']
        // by stancl/tenancy's VirtualColumn trait.
        $domainName = $record->domain ?? null;

        $defaultAdminEmail    = '';
        $defaultAdminPassword = '';
        $defaultLoginUrl      = '';

        // ── 1. Create domain record ──────────────────────────────────
        if ($domainName) {
            try {
                Domain::updateOrCreate(
                    ['saas_client_id' => $record->id],
                    [
                        'domain'     => $domainName,
                        'type'       => 'subdomain',
                        'is_primary' => true,
                    ]
                );
            } catch (\Throwable $e) {
                Log::error('Domain creation failed: ' . $e->getMessage());
                Notification::make()
                    ->title('Domain Creation Failed')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        }

        // ── 2. Trial subscription ────────────────────────────────────
        try {
            if ($record->plan_id) {
                \App\Models\Subscription::startTrial($record->id, $record->plan_id);
            }
        } catch (\Throwable $e) {
            Log::error('Subscription creation failed: ' . $e->getMessage());
            Notification::make()
                ->title('Subscription Creation Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        // ── 3. Usage metric ──────────────────────────────────────────
        try {
            if (class_exists('\App\Models\UsageMetric')) {
                \App\Models\UsageMetric::firstOrCreate(
                    ['saas_client_id' => $record->id],
                    ['last_calculated_at' => now()]
                );
            }
        } catch (\Throwable $e) {
            Log::error('Usage metric creation failed: ' . $e->getMessage());
        }

        // ── 4. Admin user ────────────────────────────────────────────
        try {
            $password = Str::random(12);
            $email    = $record->email ?? ('admin@' . ($domainName ?? $record->slug . '.localhost'));

            $adminUser = User::withoutGlobalScopes()->create([
                'name'           => $record->contact_name ?? ($record->name . ' Admin'),
                'email'          => $email,
                'password'       => Hash::make($password),
                'phone_number'   => $record->phone ?? '',
                'saas_client_id' => $record->id,
            ]);

            // Assign the 'admin' role so the user has all tenant permissions
            try {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                $adminRole = \App\Models\AppRole::findOrCreate('admin', 'web');
                $adminUser->assignRole($adminRole);
            } catch (\Throwable $e) {
                Log::warning('Role assignment failed: ' . $e->getMessage());
            }

            $defaultAdminEmail    = $email;
            $defaultAdminPassword = $password;
            $defaultLoginUrl      = rtrim(config('app.url'), '/') . "/app/{$record->slug}";

            // Send welcome email with credentials
            try {
                TenantRegistered::dispatch($adminUser, $password, $defaultLoginUrl);
            } catch (\Throwable $e) {
                Log::warning('TenantRegistered event failed: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('Admin creation failed: ' . $e->getMessage());
            Notification::make()
                ->title('Admin User Creation Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        // ── 5. Success notification with credentials ─────────────────
        if ($defaultAdminEmail && $defaultAdminPassword) {
            Notification::make()
                ->title('SaaS Client Created Successfully')
                ->body(
                    "Login credentials and login URL have been sent to {$defaultAdminEmail}.\n\n" .
                    "Login URL: {$defaultLoginUrl}\n" .
                    "Email: {$defaultAdminEmail}\n" .
                    "Password: {$defaultAdminPassword}\n\n" .
                    "Save these credentials — the password cannot be recovered."
                )
                ->success()
                ->persistent()
                ->send();
        }
    }
}
