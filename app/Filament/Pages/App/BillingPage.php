<?php

namespace App\Filament\Pages\App;

use App\Models\Subscription;
use App\Services\MpesaService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

/**
 * BillingPage
 *
 * Tenant-facing subscription & billing page.
 * Shows current plan, status, trial/expiry dates, usage limits,
 * and provides an M-Pesa STK-push payment button to renew the subscription.
 */
class BillingPage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Billing & Subscription';

    protected static ?string $title = 'Billing & Subscription';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.app.billing-page';

    // Phone number bound to the M-Pesa payment form
    public string $mpesaPhone = '';

    public function getSubscription(): ?Subscription
    {
        return filament()->getTenant()?->subscription?->load('plan');
    }

    // ── Computed view data ───────────────────────────────────────────────────

    public function getStatusBadgeColor(): string
    {
        return match ($this->getSubscription()?->status) {
            'active'    => 'success',
            'trialing'  => 'warning',
            'grace'     => 'warning',
            'expired'   => 'danger',
            'cancelled' => 'danger',
            default     => 'gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->getSubscription()?->status) {
            'active'    => 'Active',
            'trialing'  => 'Trial',
            'grace'     => 'Grace Period',
            'expired'   => 'Expired',
            'cancelled' => 'Cancelled',
            default     => 'Unknown',
        };
    }

    public function getDaysRemaining(): ?int
    {
        $sub = $this->getSubscription();
        if (!$sub || !$sub->ends_at) {
            return null;
        }

        return (int) now()->diffInDays($sub->ends_at, false);
    }

    public function getUsageMetric(): ?object
    {
        return filament()->getTenant()?->usageMetric;
    }

    // ── Page header actions ──────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payWithMpesa')
                ->label('Pay via M-Pesa')
                ->icon('heroicon-o-device-phone-mobile')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('M-Pesa STK Push Payment')
                ->modalDescription('Enter the Safaricom phone number (e.g. 254712345678) to receive the payment prompt.')
                ->form([
                    TextInput::make('phone')
                        ->label('M-Pesa Phone Number')
                        ->placeholder('254712345678')
                        ->tel()
                        ->required()
                        ->rule('regex:/^2547[0-9]{8}$/')
                        ->helperText('Format: 254XXXXXXXXX (12 digits, starts with 254)'),
                ])
                ->action(function (array $data): void {
                    $tenant       = filament()->getTenant();
                    $subscription = $tenant?->subscription;

                    if (!$subscription) {
                        Notification::make()
                            ->title('No active subscription found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        $mpesa  = new MpesaService();
                        $result = $mpesa->stkPush(
                            phone:        $data['phone'],
                            amount:       (float) $subscription->getAmount(),
                            reference:    strtoupper(substr($tenant->slug, 0, 12)),
                            description:  'Subscription',
                        );

                        if ($result['success'] ?? false) {
                            Notification::make()
                                ->title('Payment request sent!')
                                ->body('Check your phone for the M-Pesa prompt. Enter your PIN to complete payment.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Payment initiation failed')
                                ->body($result['message'] ?? 'Unknown error. Please try again.')
                                ->danger()
                                ->send();
                        }
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
