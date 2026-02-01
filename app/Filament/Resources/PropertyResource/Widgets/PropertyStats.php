<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyStats extends BaseWidget
{
    public ?Model $record = null;

    public bool $isAggregate = false;

    protected function getStats(): array
    {
        // Force aggregate mode on list pages (no single record)
        if ($this->isAggregate || is_null($this->record)) {
            return $this->getAggregateStats();
        }

        /** @var Property $property */
        $property = $this->record;

        if (! $property instanceof Property) {
            return [
                Stat::make('No Property Loaded', 'N/A')
                    ->description('Check if on View/Edit page')
                    ->color('warning'),
            ];
        }

        // ── Single Property Stats ───────────────────────────────────────────────

        $property->loadCount([
            'units',
            'units as occupied_units_count' => fn (Builder $q) => $q->whereHas('tenancyAgreements', fn ($q) => $this->activeTenancyQuery($q)),
        ]);

        $totalUnits    = $property->units_count ?? $property->number_of_units ?? 0;
        $occupiedUnits = $property->occupied_units_count ?? 0;
        $vacantUnits   = max(0, $totalUnits - $occupiedUnits);
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0.0;

        $activeTenancies = $property->tenancyAgreements()
            ->where(fn ($q) => $this->activeTenancyQuery($q))
            ->count();

        // Fixed: use the actual column 'amount'
        $totalMonthlyRent = $property->tenancyAgreements()
            ->where(fn ($q) => $this->activeTenancyQuery($q))
            ->sum('amount');

        // Placeholder (implement later if needed)
        $overdueAmount    = 0;
        $meterIssuesCount = 0;

        $expiringCount = $property->tenancyAgreements()
            ->where(fn ($q) => $this->activeTenancyQuery($q))
            ->whereBetween('end_date', [now(), now()->addDays(60)])
            ->count();

        return [
            Stat::make('Total Units', $totalUnits)
                ->description('All registered units')
                ->icon('heroicon-o-home')
                ->color('gray'),

            Stat::make('Occupied / Vacant', "{$occupiedUnits} / {$vacantUnits}")
                ->description('Currently occupied / available')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Occupancy Rate', number_format($occupancyRate, 1) . '%')
                ->description('Percentage occupied')
                ->color($occupancyRate >= 80 ? 'success' : ($occupancyRate >= 50 ? 'warning' : 'danger')),

            Stat::make('Monthly Rent (Active)', 'KES ' . number_format($totalMonthlyRent))
                ->description('From active leases')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Active Agreements', $activeTenancies)
                ->description('Current tenancy agreements')
                ->icon('heroicon-o-document-text'),

            Stat::make('Overdue Amount', 'KES ' . number_format($overdueAmount))
                ->description('Pending past due')
                ->color($overdueAmount > 0 ? 'danger' : 'success'),

            Stat::make('Units w/ Meter Issues', $meterIssuesCount)
                ->description('Recent reported issues')
                ->color($meterIssuesCount > 0 ? 'danger' : 'gray'),

            Stat::make('Leases Expiring ≤60d', $expiringCount)
                ->description('Upcoming expirations')
                ->color($expiringCount > 0 ? 'warning' : 'gray'),
        ];
    }

    protected function getAggregateStats(): array
    {
        $properties = Property::accessibleByUser(auth()->user())->get();

        $totalProperties   = $properties->count();
        $totalUnits        = $properties->sum('number_of_units');

        $totalOccupied     = 0;
        $totalActiveRent   = 0;
        $totalActiveAgreements = 0;

        foreach ($properties as $property) {
            $property->loadCount([
                'units as occupied_units_count' => fn (Builder $q) => $q->whereHas('tenancyAgreements', fn ($q) => $this->activeTenancyQuery($q)),
            ]);

            $occupied = $property->occupied_units_count ?? 0;
            $totalOccupied += $occupied;

            $activeAgreements = $property->tenancyAgreements()
                ->where(fn ($q) => $this->activeTenancyQuery($q))
                ->count();

            $totalActiveAgreements += $activeAgreements;

          
            $rent = $property->tenancyAgreements()
                ->where(fn ($q) => $this->activeTenancyQuery($q))
                ->sum('amount');

            $totalActiveRent += $rent ?? 0;
        }

        $overallOccupancy = $totalUnits > 0 ? round(($totalOccupied / $totalUnits) * 100, 1) : 0;

        return [
            Stat::make('Properties', $totalProperties)
                ->description('Visible to you')
                ->icon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Total Units', $totalUnits)
                ->icon('heroicon-o-home')
                ->color('gray'),

            Stat::make('Occupied Units', $totalOccupied)
                ->description("Overall occupancy: {$overallOccupancy}%")
                ->icon('heroicon-o-check-circle')
                ->color($overallOccupancy >= 80 ? 'success' : ($overallOccupancy >= 50 ? 'warning' : 'danger')),

            Stat::make('Total Monthly Rent', 'KES ' . number_format($totalActiveRent))
                ->description('Active leases across properties')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Active Agreements', $totalActiveAgreements)
                ->description('Total active tenancies')
                ->icon('heroicon-o-document-text')
                ->color('blue'),
        ];
    }

    private function activeTenancyQuery(Builder $query): void
    {
        $query->where('start_date', '<=', now())
              ->where(function ($q) {
                  $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
              });
    }
}
