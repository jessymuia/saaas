<?php

namespace App\Filament\Resources\App\ManualInvoicesResource\RelationManagers;

use App\Models\RefBillingType;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ManualInvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'manualInvoiceItems';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('bill_date')->required(),
            Forms\Components\DatePicker::make('due_date')
                ->rules('after:bill_date')
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->live()
                ->afterStateUpdated(function ($get, $set) {
                    $amount = (float) ($get('amount') ?? 0);
                    $isVatable = $get('is_vatable');
                    $vat = $isVatable ? round(AppUtils::VAT_RATE * $amount, 2) : 0;
                    $set('vat', $vat);
                    $set('total_amount', round($amount + $vat, 2));
                }),
            Forms\Components\Checkbox::make('is_vatable')
                ->live()
                ->default(false)
                ->afterStateUpdated(function ($get, $set) {
                    $amount = (float) ($get('amount') ?? 0);
                    $isVatable = $get('is_vatable');
                    $vat = $isVatable ? round(AppUtils::VAT_RATE * $amount, 2) : 0;
                    $set('vat', $vat);
                    $set('total_amount', round($amount + $vat, 2));
                }),
            Forms\Components\TextInput::make('vat')
                ->readOnly()
                ->live()
                ->default(0)
                ->numeric(),
            Forms\Components\TextInput::make('total_amount')
                ->readOnly()
                ->live()
                ->default(0)
                ->numeric(),
            Forms\Components\Select::make('billing_type_id')
                ->options(fn () => RefBillingType::pluck('type', 'id'))
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('bill_date')->date('F j, Y')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date('F j, Y')->sortable(),
                Tables\Columns\TextColumn::make('amount')->sortable(),
                Tables\Columns\TextColumn::make('vat')->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->sortable(),
                Tables\Columns\TextColumn::make('billingType.type')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date('F j, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->date('F j, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ]);
    }
}
