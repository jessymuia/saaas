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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        if (!$record) {
            Log::error('Record not found after creation');
            return;
        }

        // Get domain from the record's data JSON field
        $domainName = $record->data['domain'] ?? null;

        // Initialize variables for notification
        $defaultAdminEmail = '';
        $defaultAdminPassword = '';
        $defaultLoginUrl = '';

        /**
         * Create domain
         */
        if ($domainName) {
            try {
                Domain::updateOrCreate(
                    ['saas_client_id' => $record->id],
                    ['domain' => $domainName]
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
         * Start trial
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
         * Create usage metric
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
         * Create admin user
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
         * Send success notification with credentials
         */
        if ($defaultAdminEmail && $defaultAdminPassword) {
            Notification::make()
                ->title('SaaS Client Created Successfully')
                ->body(
                    "Login URL: {$defaultLoginUrl}\n" .
                    "Email: {$defaultAdminEmail}\n" .
                    "Password: {$defaultAdminPassword}"
                )
                ->success()
                ->persistent()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}