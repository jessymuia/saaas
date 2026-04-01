<?php

namespace App\Filament\Resources\App\InvoiceResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema; // v4 specific
use Filament\Tables\Columns\TextColumn;

class TenancyBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyBills';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Bill Name')
                ->required()
                ->maxLength(500),

            \Filament\Forms\Components\Select::make('billing_type_id')
                ->label('Billing Type')
                ->required()
                ->options(\App\Models\RefBillingType::pluck('type', 'id'))
                ->preload(),

            \Filament\Forms\Components\DatePicker::make('bill_date')
                ->label('Bill Date')
                ->required(),

            \Filament\Forms\Components\DatePicker::make('due_date')
                ->label('Due Date')
                ->required(),

            \Filament\Forms\Components\TextInput::make('amount')
                ->label('Amount (KES)')
                ->numeric()
                ->required()
                ->minValue(0)
                ->live()
                ->afterStateUpdated(function ($get, $set) {
                    $set('total_amount', round((float) $get('amount') + (float) $get('vat'), 2));
                }),

            \Filament\Forms\Components\TextInput::make('vat')
                ->label('VAT (KES)')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->live()
                ->afterStateUpdated(function ($get, $set) {
                    $set('total_amount', round((float) $get('amount') + (float) $get('vat'), 2));
                }),

            \Filament\Forms\Components\TextInput::make('total_amount')
                ->label('Total Amount (KES)')
                ->numeric()
                ->required()
                ->minValue(0),

            \Filament\Forms\Components\Select::make('service_id')
                ->label('Service (optional)')
                ->nullable()
                ->options(\App\Models\Services::pluck('name', 'id'))
                ->preload(),

            \Filament\Forms\Components\Select::make('utility_id')
                ->label('Utility (optional)')
                ->nullable()
                ->options(\App\Models\RefUtility::pluck('name', 'id'))
                ->preload(),

            \Filament\Forms\Components\Toggle::make('is_deposit')
                ->label('Is Deposit?')
                ->default(false),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $invoice = $this->getOwnerRecord();
        $data['tenancy_agreement_id'] = $invoice->tenancy_agreement_id;
        $data['saas_client_id']       = $invoice->saas_client_id;
        return $data;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Bill Name')->searchable(),
                TextColumn::make('billingType.type')->label('Billing Type'),
                TextColumn::make('bill_date')->date()->sortable(),
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('amount')->money('KES')->sortable(),
                TextColumn::make('vat')->money('KES')->sortable(),
                TextColumn::make('total_amount')->money('KES')->sortable(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
            ]);
    }
}
