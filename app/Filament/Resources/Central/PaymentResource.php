<?php

namespace App\Filament\Resources\Central;

use App\Models\InvoicePayment;
use App\Models\SaasClient;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\Central\PaymentResource\Pages;

class PaymentResource extends Resource
{
    protected static ?string $model = InvoicePayment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'SaaS Management';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Forms\Components\Select::make('saas_client_id')
                ->label('Client')
                ->options(SaasClient::orderBy('name')->pluck('name', 'id'))
                ->disabled(),

            \Filament\Forms\Components\TextInput::make('invoice_id')
                ->label('Invoice ID')
                ->disabled(),

            \Filament\Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->prefix('KES')
                ->disabled(),

            \Filament\Forms\Components\TextInput::make('paid_by')
                ->label('Paid By')
                ->disabled(),

            \Filament\Forms\Components\TextInput::make('payment_reference')
                ->label('Reference')
                ->disabled(),

            \Filament\Forms\Components\DateTimePicker::make('payment_date')
                ->label('Payment Date')
                ->disabled(),

            \Filament\Forms\Components\Textarea::make('description')
                ->label('Notes')
                ->disabled()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('saasClient.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_id')
                    ->label('Invoice')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('KES')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_by')
                    ->label('Paid By')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_reference')
                    ->label('Reference')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_confirmed')
                    ->label('Confirmed?')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('saas_client_id')
                    ->label('Client')
                    ->options(SaasClient::orderBy('name')->pluck('name', 'id')),

                Tables\Filters\TernaryFilter::make('is_confirmed')
                    ->label('Confirmed'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'view'  => Pages\ViewPayment::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
