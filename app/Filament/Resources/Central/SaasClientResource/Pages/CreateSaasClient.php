<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Events\TenantRegistered;
use App\Filament\Resources\Central\SaasClientResource;
use App\Models\Domain;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateSaasClient extends CreateRecord
{
    protected static string $resource = SaasClientResource::class;

    /**
     * Sanitise form data before it reaches the model setters.
     * Removes nullable branding fields when empty so their mutators
     * never receive a null value for a non-nullable typed argument.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (array_key_exists('primary_color', $data) && $data['primary_color'] === null) {
            unset($data['primary_color']);
        }

        if (array_key_exists('logo_path', $data) && empty($data['logo_path'])) {
            unset($data['logo_path']);
        }

        return $data;
    }

    /**
     * Wrap the Eloquent save in a try/catch so any unexpected exception
     * (type errors, DB constraint failures, etc.) surfaces as a
     * Filament danger notification instead of an Ignition crash page.
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\Throwable $e) {
            Log::error('SaasClient creation failed: ' . $e->getMessage(), [
                'data'  => $data,
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title('Client could not be created')
                ->body($this->friendlyErrorMessage($e))
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    /**
     * Convert a raw exception into a short, readable message for the UI.
     */
    private function friendlyErrorMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'unique') || str_contains($message, 'duplicate')) {
            return 'A client with this slug or email already exists. Please use a different value.';
        }

        if ($e instanceof \TypeError) {
            return 'One or more fields contain an invalid value. Please review the form and try again.';
        }

        return 'An unexpected error occurred: ' . $message;
    }

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
                    ->title('Domain Setup Failed')
                    ->body('The client was created but the domain could not be saved: ' . $e->getMessage())
                    ->warning()
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
                ->title('Subscription Setup Failed')
                ->body('The client was created but the subscription could not be initialised: ' . $e->getMessage())
                ->warning()
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

            try {
                TenantRegistered::dispatch($adminUser, $password, $defaultLoginUrl);
            } catch (\Throwable $e) {
                Log::warning('TenantRegistered event failed: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('Admin creation failed: ' . $e->getMessage());
            Notification::make()
                ->title('Admin User Setup Failed')
                ->body('The client was created but the admin account could not be set up: ' . $e->getMessage())
                ->warning()
                ->send();
        }

        // ── 5. Success notification with credentials ─────────────────
        if ($defaultAdminEmail && $defaultAdminPassword) {
            Notification::make()
                ->title('SaaS Client Created Successfully')
                ->body(
                    "Login URL: {$defaultLoginUrl}\n" .
                    "Email: {$defaultAdminEmail}\n" .
                    "Password: {$defaultAdminPassword}\n\n" .
                    "Credentials have been sent to {$defaultAdminEmail}. Save the password — it cannot be recovered."
                )
                ->success()
                ->persistent()
                ->send();
        }
    }
}
