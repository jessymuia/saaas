<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyOwnersResource\Pages;
use App\Filament\Resources\PropertyOwnersResource\RelationManagers;
use App\Models\Property;
use App\Models\PropertyOwners;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyOwnersResource extends Resource
{
    protected static ?string $model = PropertyOwners::class;

    protected static ?string $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('property_id')
                    ->label('Property')
                    ->required()
                    ->options(
                        // pick properties without owners
                        Property::query()
                            ->whereDoesntHave('propertyOwners')
                            ->get()
                            ->mapWithKeys(fn ($property) => [$property->id => $property->name])
                    ),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->nullable()
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.name')
                    ->searchable()
                    ->sortable()
                    ->label('Property'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn ($data) => [
                        'updated_by' => auth()->user()->id,
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation("Are you sure you want to delete this property owner?")
                    ->mutateFormDataUsing(fn ($data) => [
                        'deleted_by' => auth()->user()->id,
                    ]),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListPropertyOwners::route('/'),
            'create' => Pages\CreatePropertyOwners::route('/create'),
            'edit' => Pages\EditPropertyOwners::route('/{record}/edit'),
        ];
    }
}
