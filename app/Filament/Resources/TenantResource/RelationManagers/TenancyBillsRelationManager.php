<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use App\Filament\Exports\TenancyBillsExporter;
use App\Filament\Exports\TenantExporter;
use App\Models\Invoice;
use App\Models\MeterReading;
use App\Models\TenancyAgreement;
use App\Utils\AppUtils;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenancyBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyBills';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->numeric()
//                    ->visible(// if billing is name contains the text 'Garbage'
//                        fn(Get $get) => str_contains($get('name'), 'Garbage')
////                            && ($form->getOperation() === 'edit' || auth()->user()->hasRole('admin'))
//                    )
                    ->required(),
                Forms\Components\TextInput::make('vat')
                    ->numeric()
//                    ->visible(// if billing is name contains the text 'Garbage'
//                        fn(Get $get) => str_contains($get('name'), 'Garbage')
////                            && ($form->getOperation() === 'edit' || auth()->user()->hasRole('admin'))
//                    )
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric()// TODO: FLAG:MIGRATION Add automatic recalculation of total amount
//                    ->visible(// if billing is name contains the text 'Garbage'
//                        fn(Get $get) => str_contains($get('name'), 'Garbage')
////                            && ($form->getOperation() === 'edit' || auth()->user()->hasRole('admin'))
//                    )
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
                    ->numeric(2)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric(2)
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
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('generate-bills')
                    ->action(function (): void{
                        // handle errors
                        AppUtils::generateBills();
                    })
                    ->requiresConfirmation(" Are you sure you want to generate bills for this month? Please verify the meter readings before proceeding "),
                Tables\Actions\Action::make('generate-bills-for-next-month')
                    ->action(function (): void{
                        AppUtils::generateBills(isBillsForNextMonth: true);
                    })
                    ->requiresConfirmation(" Are you sure you want to generate bills for coming month?"),
                ExportAction::make()
                    ->exporter(TenancyBillsExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(fn () => 'Are you sure you want to delete this tenancy bill?')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(TenancyBillsExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
