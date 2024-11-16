<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Filament\Exports\PropertyExporter;
use App\Rules\CheckOccupancyOfUnit;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenancyAgreementsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenancyAgreements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->required()
                    ->relationship('tenant', 'name'),
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->required()
                    ->disabledOn('edit')
                    ->relationship('unit', 'name', function (Builder $query) {
                        $query->where('property_id', $this->ownerRecord->id);
                    })
                    ->rules([
                        fn (Get $get) : CheckOccupancyOfUnit => new CheckOccupancyOfUnit($get('start_date'), $form->getOperation()),
                    ]),
                Forms\Components\Select::make('billing_type_id')
                    ->label('Billing Type')
                    ->required()
                    ->relationship('billingType', 'type'),
                Forms\Components\Select::make('agreement_type_id')
                    ->label('Agreement Type')
                    ->required()
                    ->reactive()
                    ->relationship('agreementType', 'type'),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->nullable(fn(Get $get) => $get('agreement_type_id') == 2)
                    ->visible(fn (Get $get) => $get('agreement_type_id') == 1)
                    ->reactive()
                    ->after('start_date'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('deposit_amount')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('balance_carried_forward')
                    ->nullable()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Checkbox::make('is_escalation')
                    ->label('Define Escalation')
                    ->reactive(),
                Forms\Components\TextInput::make('escalation_rate')
                    ->label('Escalation Rate')
                    ->numeric()
                    ->maxValue(100)
                    ->visible(function (Get $get){
                        return $get('is_escalation') == true;
                    })
                    ->requiredIf('is_escalation',true)
                    ->reactive(),
                Forms\Components\TextInput::make('escalation_period_in_months')
                    ->label('Escalation Period(months)')
                    ->numeric()
                    ->reactive()
                    ->visible(function (Get $get){
                        return $get('is_escalation') == true;
                    })
//                    ->disabledOn('edit')
                    ->afterStateUpdated(function (Get $get,Forms\Set $set){
                        $set('next_escalation_date',today()->addMonths($get('escalation_period_in_months'))->format('Y-m-d'));
                    })
                    ->requiredIf('is_escalation',true),
                Forms\Components\DatePicker::make('next_escalation_date')
                    ->visible(function (Get $get) use ($form) {
                        return ($get('is_escalation') == true && $form->getOperation() == 'create');
                    })
                    ->reactive()
                    ->readOnly(),
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
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('billingType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('agreementType.type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deposit_amount')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('escalation_rate')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('escalation_period_in_months')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('next_escalation_date')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('balance_carried_forward')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->mutateFormDataUsing(function ($data) {
                        $data['created_by'] = auth()->user()->id;

                        return $data;
                    }),
                ExportAction::make()
                    ->exporter(PropertyExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function ($data) {
                        $data['updated_by'] = auth()->user()->id;

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(fn ($record) => "Are you sure you want to delete this record?")
                    ->mutateFormDataUsing(function ($data) {
                        $data['deleted_by'] = auth()->user()->id;

                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(PropertyExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }
}
