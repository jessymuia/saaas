<?php

namespace App\Filament\Resources\ManualInvoicesResource\RelationManagers;

use App\Filament\Exports\ManualInvoicesExporter;
use App\Models\RefBillingType;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManualInvoiceRelationManager extends RelationManager
{
    protected static string $relationship = 'manualInvoiceItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('bill_date')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->rules('after:bill_date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Get $get,Forms\Set $set) {
                        $isVatable = $get('is_vatable');

                        $amount = $get('amount') ?? 0.0;
                        $amount = '' ?  0 : $amount;

                        if ($isVatable) {
                            $set('vat', AppUtils::VAT_RATE * $amount);
                        }else {
                            $set('vat', 0.0);
                        }

                        $set('total_amount', ($amount + $get('vat') ));
                    }),
                Forms\Components\Checkbox::make('is_vatable')
                    ->reactive()
                    ->default(false)
                    ->afterStateUpdated(function (Forms\Get $get,Forms\Set $set) {
                        $isVatable = $get('is_vatable');

                        $amount = $get('amount') ?? 0.0;
                        $amount = '' ?  0.0 : $amount;

                        if ($isVatable) {
                            $set('vat', AppUtils::VAT_RATE * $amount);
                        } else {
                            $set('vat', 0.0);
                        }
                        $set('total_amount', ($amount + $get('vat')));
                    }),
                Forms\Components\TextInput::make('vat')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->default(0)
                    ->type('number'),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->readOnly()
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Get $get,Forms\Set $set) {
                        $amount = $get('amount') ?? 0;
                        $vat = $get('vat') ?? 0;

                        $set('total_amount', $amount + $vat);
                    })
                    ->type('number'),
                Forms\Components\Select::make('billing_type_id')
                    ->options(RefBillingType::get()->pluck('type', 'id')->toArray())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['created_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['updated_by'] = auth()->id();

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['deleted_by'] = auth()->id();

                        return $data;
                    })
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ManualInvoicesExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(ManualInvoicesExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
