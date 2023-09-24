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
                Forms\Components\TextInput::make('created_by')
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->numeric(),
                Forms\Components\TextInput::make('deleted_by')
                    ->numeric(),
                Forms\Components\TextInput::make('tenancy_agreement_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_type_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('received_by')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('status')
                    ->required(),
                Forms\Components\Toggle::make('archive')
                    ->required(),
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
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancy_agreement_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('received_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('archive')
                    ->boolean(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_reference')
                    ->searchable(),
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
                    Tables\Actions\DeleteBulkAction::make(),
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
