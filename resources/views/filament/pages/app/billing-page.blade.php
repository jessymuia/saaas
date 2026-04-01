<x-filament-panels::page>
    @php
        $subscription = $this->getSubscription();
        $plan         = $subscription?->plan;
        $usage        = $this->getUsageMetric();
        $daysLeft     = $this->getDaysRemaining();
        $statusLabel  = $this->getStatusLabel();
        $statusColor  = $this->getStatusBadgeColor();
    @endphp

    <div class="space-y-6">

        {{-- ── Subscription Status Card ─────────────────────────────────────── --}}
        <x-filament::section>
            <x-slot name="heading">Subscription Status</x-slot>

            @if ($subscription)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                    {{-- Status badge --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            Status
                        </span>
                        <x-filament::badge :color="$statusColor" size="lg">
                            {{ $statusLabel }}
                        </x-filament::badge>
                    </div>

                    {{-- Plan --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            Current Plan
                        </span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $plan?->name ?? '—' }}
                        </span>
                        @if ($plan)
                            <span class="text-sm text-gray-500">
                                KES {{ number_format($plan->price_monthly, 2) }} / month
                            </span>
                        @endif
                    </div>

                    {{-- Days remaining --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            Renewal / Expiry
                        </span>
                        @if ($subscription->ends_at)
                            <span class="text-lg font-semibold {{ $daysLeft !== null && $daysLeft < 7 ? 'text-danger-600' : 'text-gray-900 dark:text-white' }}">
                                {{ $daysLeft !== null ? abs($daysLeft) . ' days ' . ($daysLeft < 0 ? 'overdue' : 'remaining') : '—' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Expires {{ $subscription->ends_at->format('d M Y') }}
                            </span>
                        @else
                            <span class="text-gray-500">No expiry set</span>
                        @endif
                    </div>
                </div>

                {{-- Trial / grace banners --}}
                @if ($subscription->status === 'trialing' && $subscription->trial_ends_at)
                    <div class="mt-4 rounded-lg border border-warning-200 bg-warning-50 px-4 py-3 text-sm text-warning-800 dark:border-warning-700 dark:bg-warning-900/20 dark:text-warning-300">
                        <strong>Trial active</strong> — Your free trial ends
                        <strong>{{ $subscription->trial_ends_at->format('d M Y') }}</strong>.
                        Add a payment method before it expires to avoid interruption.
                    </div>
                @endif

                @if ($subscription->status === 'grace')
                    <div class="mt-4 rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-800 dark:border-danger-700 dark:bg-danger-900/20 dark:text-danger-300">
                        <strong>Grace period</strong> — Your subscription has expired.
                        Grace period ends
                        <strong>{{ $subscription->grace_ends_at?->format('d M Y') ?? 'soon' }}</strong>.
                        Please pay now to restore full access.
                    </div>
                @endif

            @else
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800/50">
                    No subscription found for this account. Please contact support.
                </div>
            @endif
        </x-filament::section>

        {{-- ── Plan Limits & Usage ───────────────────────────────────────────── --}}
        @if ($plan)
            <x-filament::section>
                <x-slot name="heading">Plan Limits & Usage</x-slot>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                    @php
                        $metrics = [
                            ['label' => 'Properties', 'limit' => $plan->max_properties, 'used' => $usage?->properties_count ?? 0],
                            ['label' => 'Units',       'limit' => $plan->max_units,       'used' => $usage?->units_count ?? 0],
                            ['label' => 'Users',       'limit' => $plan->max_users,        'used' => $usage?->users_count ?? 0],
                        ];
                    @endphp

                    @foreach ($metrics as $metric)
                        @php
                            $pct = $metric['limit'] > 0 ? min(100, round(($metric['used'] / $metric['limit']) * 100)) : 0;
                            $barColor = $pct >= 90 ? 'bg-danger-500' : ($pct >= 70 ? 'bg-warning-500' : 'bg-primary-500');
                        @endphp
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $metric['label'] }}</span>
                                <span class="text-gray-500">{{ $metric['used'] }} / {{ $metric['limit'] ?? '∞' }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-2 rounded-full {{ $barColor }} transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ── Payment Instructions ─────────────────────────────────────────── --}}
        <x-filament::section>
            <x-slot name="heading">How to Pay</x-slot>

            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <li>Click <strong>Pay via M-Pesa</strong> above.</li>
                <li>Enter your Safaricom number (format: <code>2547XXXXXXXX</code>).</li>
                <li>An STK push prompt will appear on your phone — enter your M-Pesa PIN.</li>
                <li>Your subscription will be renewed automatically once payment is confirmed.</li>
            </ol>

            <p class="mt-4 text-xs text-gray-400">
                Payments are processed securely via Safaricom Daraja API.
                For support contact <a href="mailto:support@yoursaas.com" class="underline">support@yoursaas.com</a>.
            </p>
        </x-filament::section>

    </div>
</x-filament-panels::page>
