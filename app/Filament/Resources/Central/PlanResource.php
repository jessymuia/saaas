<?php

namespace App\Filament\Resources\Central;

use App\Models\Plan;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    
    protected static \UnitEnum|string|null $navigationGroup = 'SaaS Management';
    
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan Details')
                ->schema([
                    TextInput::make('name')
                        ->required(),
                        
                    TextInput::make('price_monthly')
                        ->numeric()
                        ->prefix('$')
                        ->required(),
                        
                    KeyValue::make('limits')
                        ->helperText('Define limits like max_properties, max_users, etc.')
                        ->keyLabel('Feature Name')
                        ->valueLabel('Limit Value'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('price_monthly')
                ->money('usd')
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            //
        ])
        ->actions([
            EditAction::make(),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}
    
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Central\PlanResource\Pages\ListPlans::route('/'),
            'create' => \App\Filament\Resources\Central\PlanResource\Pages\CreatePlan::route('/create'),
            'edit' => \App\Filament\Resources\Central\PlanResource\Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}