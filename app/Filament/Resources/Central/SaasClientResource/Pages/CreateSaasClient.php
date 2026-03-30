<?php

namespace App\Filament\Resources\Central\SaasClientResource\Pages;

use App\Filament\Resources\Central\SaasClientResource;
use App\Models\Domain;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateSaasClient extends CreateRecord
{
    protected static string $resource = SaasClientResource::class;

    protected string $defaultAdminEmail    = '';
    protected string $defaultAdminPassword = '';
    protected string $defaultLoginUrl      = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['domain']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $domain = $this->data['domain'] ?? null;

        // 1. Create domain
        try {
            if ($domain) {
                $subdomain = explode('.', $domain)[0];
                Domain::create([
                    'domain'         => $subdomain,
                    'saas_client_id' => $record->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Domain creation failed: ' . $e->getMessage());
        }

        // 2. Start trial subscription 
        try {
            if ($record->plan_id && Schema::hasTable('subscriptions')) {
                \App\Models\Subscription::startTrial($record->id, $record->plan_id);
            }
        } catch (\Throwable $e) {
            Log::error('Subscription creation failed: ' . $e->getMessage());
        }

        // 3. Create usage metric 
        try {
            if (Schema::hasTable('usage_metrics')) {
                \App\Models\UsageMetric::firstOrCreate(
                    ['saas_client_id' => $record->id],
                    ['last_calculated_at' => now()]
                );
            }
        } catch (\Throwable $e) {
            Log::error('UsageMetric creation failed: ' . $e->getMessage());
        }

        // 4. Create default admin user 
        try {
            if (Schema::hasTable('users')) {
                DB::statement("SET app.current_tenant_id = '{$record->id}'");

                $password = Str::random(12);
                $email    = $record->email ?? 'admin@' . ($domain ?? $record->slug . '.localhost');
                $loginUrl = 'http://' . ($domain ?? $record->slug . '.localhost:8000') . '/app/login';

                User::create([
                    'name'           => $record->contact_name ?? $record->name . ' Admin',
                    'email'          => $email,
                    'password'       => Hash::make($password),
                    'saas_client_id' => $record->id,
                ]);

                $this->defaultAdminEmail    = $email;
                $this->defaultAdminPassword = $password;
                $this->defaultLoginUrl      = $loginUrl;

                // Send welcome email
                if ($record->email) {
                    try {
                        $record->notify(new \App\Notifications\TenantWelcomeNotification(
                            tenantName: $record->name,
                            loginUrl: $loginUrl,
                            email: $email,
                            password: $password,
                        ));
                    } catch (\Throwable $e) {
                        Log::error('Welcome email failed: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Default admin user creation failed: ' . $e->getMessage());
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreatedNotification(): void
    {
        if ($this->defaultAdminEmail) {
            Notification::make()
                ->title('✅ SaaS Client Created Successfully')
                ->body(
                    "Login URL: {$this->defaultLoginUrl}\n" .
                    "Email: {$this->defaultAdminEmail}\n" .
                    "Password: {$this->defaultAdminPassword}\n\n" .
                    "A welcome email has been sent to the client."
                )
                ->success()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title('✅ SaaS Client Created')
                ->body('Client created successfully. Run pending migrations to enable full features.')
                ->success()
                ->send();
        }
    }
}