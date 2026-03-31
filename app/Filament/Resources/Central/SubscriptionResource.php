<?php

namespace App\Filament\Resources\Central;

use App\Models\Plan;
use App\Models\SaasClient;
use App\Models\Subscription;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\Central\SubscriptionResource\Pages;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'SaaS Management';

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Forms\Components\Select::make('saas_client_id')
                ->label('Client')
                ->options(SaasClient::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            \Filament\Forms\Components\Select::make('plan_id')
                ->label('Plan')
                ->options(Plan::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            \Filament\Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'trialing'     => 'Trialing',
                    'active'       => 'Active',
                    'grace_period' => 'Grace Period',
                    'expired'      => 'Expired',
                    'suspended'    => 'Suspended',
                    'canceled'     => 'Canceled',
                ])
                ->required()
                ->default('trialing'),

            \Filament\Forms\Components\Select::make('billing_cycle')
                ->label('Billing Cycle')
                ->options([
                    'monthly'   => 'Monthly',
                    'quarterly' => 'Quarterly',
                    'annual'    => 'Annual',
                ])
                ->required()
                ->default('monthly'),

            \Filament\Forms\Components\DateTimePicker::make('starts_at')
                ->label('Starts At')
                ->required()
                ->default(now()),

            \Filament\Forms\Components\DateTimePicker::make('ends_at')
                ->label('Ends At')
                ->required()
                ->default(now()->addDays(14))
                ->after('starts_at'),

            \Filament\Forms\Components\DateTimePicker::make('trial_ends_at')
                ->label('Trial Ends At')
                ->nullable(),

            \Filament\Forms\Components\DateTimePicker::make('grace_ends_at')
                ->label('Grace Period Ends At')
                ->nullable(),

            \Filament\Forms\Components\DateTimePicker::make('cancelled_at')
                ->label('Cancelled At')
                ->nullable(),

            \Filament\Forms\Components\TextInput::make('reminder_count')
                ->label('Reminder Count')
                ->numeric()
                ->default(0)
                ->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('saasClient.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active'       => 'success',
                        'trialing'     => 'info',
                        'grace_period' => 'warning',
                        'expired'      => 'danger',
                        'suspended'    => 'danger',
                        'canceled'     => 'gray',
                        default        => 'gray',
                    }),

                \Filament\Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Billing')
                    ->badge(),

                \Filament\Tables\Columns\TextColumn::make('starts_at')
                    ->label('Started')
                    ->dateTime('d M Y')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('ends_at')
                    ->label('Ends')
                    ->dateTime('d M Y')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Trial Ends')
                    ->dateTime('d M Y')
                    ->placeholder('—'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'trialing'     => 'Trialing',
                        'active'       => 'Active',
                        'grace_period' => 'Grace Period',
                        'expired'      => 'Expired',
                        'suspended'    => 'Suspended',
                        'canceled'     => 'Canceled',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit'   => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
