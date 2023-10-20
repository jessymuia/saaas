<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentPaymentResource\Pages;
use App\Filament\Resources\RentPaymentResource\RelationManagers;
use App\Models\Property;
use App\Models\RentPayment;
use App\Models\TenancyAgreement;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('property_id')
                    ->label('Property')
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function (Forms\Set $set){
                        // if its view page, then we need to get the property id from the model
                        if (request()->route()->getName() === 'filament.admin.resources.rent-payments.view') {
                            // get current record
                            $rentPayment = RentPayment::find(request()->route()->parameter('record'));
                            $set('property_id', $rentPayment->tenancyAgreement->property->id);
                        }
                    })
                    ->options(
                        fn () => Property::query()
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(
                                fn ($property) => [$property->id => $property->name]
                            )
                    ),
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->required()
                    ->reactive()
                    ->afterStateHydrated(function (Forms\Set $set){
                        // if its view page, then we need to get the property id from the model
                        if (request()->route()->getName() === 'filament.admin.resources.rent-payments.view') {
                            // get current record
                            $rentPayment = RentPayment::find(request()->route()->parameter('record'));
                            $set('unit_id', $rentPayment->tenancyAgreement->unit->id);
                        }
                    })
                    ->options(function (Get $get){
                        return Unit::query()
                            ->where('property_id', $get('property_id'))
                            ->pluck('name', 'id');
                    }),
                Forms\Components\Select::make('tenancy_agreement_id')
                    ->required()
                    ->reactive()
                    ->options(function (Get $get){
                        return TenancyAgreement::query()
                            ->where('unit_id', $get('unit_id'))
                            ->get()
                            ->mapWithKeys(fn ($tenancyAgreement) => [$tenancyAgreement->id => $tenancyAgreement->tenant->name]);
                    }),
                Forms\Components\Select::make('payment_type_id')
                    ->required()
                    ->relationship('paymentType', 'type'),
                Forms\Components\DateTimePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('paid_by')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_reference')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Additional Information')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentType.type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_reference')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean()
                    ->label('Confirmed?'),
                Tables\Columns\TextColumn::make('document_generated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_generated_by.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make()
//                        ->requiresConfirmation('Are you sure you want to delete the selected records?'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentPayments::route('/'),
            'create' => Pages\CreateRentPayment::route('/create'),
            'view' => Pages\ViewRentPayment::route('/{record}'),
            'edit' => Pages\EditRentPayment::route('/{record}/edit'),
        ];
    }
}
