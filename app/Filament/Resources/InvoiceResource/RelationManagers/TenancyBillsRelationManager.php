<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Filament\Exports\InvoiceExporter;
use App\Models\Invoice;
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

class TenancyBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyBills';

    protected static ?string $title = 'Invoice Bills';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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

            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('vat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function ($data){
                        $data['created_by'] = auth()->user()->id;
                        $data['tenancy_agreement_id'] = $this->ownerRecord->tenancy_agreement_id;

                        return $data;
                    })
                    ->hidden(function () {
                        return $this->ownerRecord->is_confirmed;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function ($data){
                        $data['updated_by'] = auth()->user()->id;

                        return $data;
                    })
                    ->hidden(function () {
                        return $this->ownerRecord->is_confirmed;
                    }),
//                Tables\Actions\DeleteAction:: make()
//                    ->requiresConfirmation()
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(InvoiceExporter::class)
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
                    ->exporter(InvoiceExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
