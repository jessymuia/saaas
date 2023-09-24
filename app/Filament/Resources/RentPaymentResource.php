<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentPaymentResource\Pages;
use App\Filament\Resources\RentPaymentResource\RelationManagers;
use App\Models\RentPayment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenancy_agreement_id')
                    ->required()
                    ->relationship('tenancyAgreement', 'id'),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_reference')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation('Are you sure you want to delete the selected records?'),
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
