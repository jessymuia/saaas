<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppRoleResource\Pages;
use App\Filament\Resources\AppRoleResource\RelationManagers;
use App\Models\AppRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppRoleResource extends Resource
{
    protected static ?string $model = AppRole::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Section::make('User Permissions')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Permissions')
                            ->columns(4)
                            ->relationship('permissions', 'name')
                            ->options(
                                \Spatie\Permission\Models\Permission::all()->pluck('name', 'id')
                            )
                    ])
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
//                Tables\Columns\TextColumn::make('guard_name')
//                    ->searchable()
//                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->searchable()
                    ->sortable(),
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
                        ->requiresConfirmation(),
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
            'index' => Pages\ListAppRoles::route('/'),
            'create' => Pages\CreateAppRole::route('/create'),
            'edit' => Pages\EditAppRole::route('/{record}/edit'),
            'view' => Pages\ViewAppRole::route('/{record}'),
        ];
    }
}
