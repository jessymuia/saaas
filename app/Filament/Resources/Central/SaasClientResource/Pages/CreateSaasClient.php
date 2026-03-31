<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

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
     * Ensure data field is properly structured before creating
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Initialize data as an array if not set
        if (!isset($data['data']) || !is_array($data['data'])) {
            $data['data'] = [];
        }
        
        // Preserve domain from nested input
        if (isset($data['data']['domain'])) {
            $domainValue = $data['data']['domain'];
            $data['data'] = ['domain' => $domainValue];
        }
        
        return $data;
    }

    /**
     * Handle post-creation setup: domains, subscriptions, users
     */
    protected function afterCreate(): void
    {
        $record = $this->record;

        if (!$record) {
            Log::error('Record not found after creation');
            return;
        }

        // Safely get domain from data field
        $domainName = null;
        if (is_array($record->data) && isset($record->data['domain'])) {
            $domainName = $record->data['domain'];
        }

        // Initialize notification variables
        $defaultAdminEmail = '';
        $defaultAdminPassword = '';
        $defaultLoginUrl = '';

        /**
         * ────────────────────────────────────────────────────────────
         * 1. CREATE DOMAIN RECORD
         * ────────────────────────────────────────────────────────────
         */
        if ($domainName) {
            try {
                Domain::updateOrCreate(
                    ['saas_client_id' => $record->id],
                    [
                        'domain' => $domainName,
                        'type' => 'subdomain',
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

        /**
         * ────────────────────────────────────────────────────────────
         * 2. START TRIAL SUBSCRIPTION
         * ────────────────────────────────────────────────────────────
         */
        try {
            if ($record->plan_id && class_exists('\App\Models\Subscription')) {
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

        /**
         * ────────────────────────────────────────────────────────────
         * 3. CREATE USAGE METRIC
         * ────────────────────────────────────────────────────────────
         */
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

        /**
         * ────────────────────────────────────────────────────────────
         * 4. CREATE ADMIN USER FOR TENANT
         * ────────────────────────────────────────────────────────────
         */
        try {
            $password = Str::random(12);
            $host = $domainName ?? $record->slug . '.localhost';
            $email = $record->email ?? 'admin@' . $host;
            $loginUrl = "http://{$host}:8000/app/login";

            User::withoutGlobalScopes()->create([
                'name' => $record->contact_name ?? $record->name . ' Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'saas_client_id' => $record->id,
            ]);

            $defaultAdminEmail = $email;
            $defaultAdminPassword = $password;
            $defaultLoginUrl = $loginUrl;

        } catch (\Throwable $e) {
            Log::error('Admin creation failed: ' . $e->getMessage());
            Notification::make()
                ->title('Admin User Creation Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        /**
         * ────────────────────────────────────────────────────────────
         * 5. SEND SUCCESS NOTIFICATION WITH CREDENTIALS
         * ────────────────────────────────────────────────────────────
         */
        if ($defaultAdminEmail && $defaultAdminPassword) {
            Notification::make()
                ->title('✅ SaaS Client Created Successfully!')
                ->body(
                    "🔗 Login URL: {$defaultLoginUrl}\n" .
                    "📧 Email: {$defaultAdminEmail}\n" .
                    "🔐 Password: {$defaultAdminPassword}\n\n" .
                    "Please save these credentials securely."
                )
                ->success()
                ->persistent()
                ->send();
        }
    }
}