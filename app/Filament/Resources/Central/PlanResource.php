<?php

namespace App\Filament\Resources\Central;

use App\Models\Plan;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static \UnitEnum|string|null $navigationGroup = 'SaaS Management';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->unique(Plan::class, 'slug', ignoreRecord: true)
                        ->helperText('Auto-generated from name — edit if needed'),
                ]),

                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),
            ]),

            Section::make('Pricing')->schema([
                Grid::make(2)->schema([
                    TextInput::make('price_monthly')
                        ->label('Monthly Price ($)')
                        ->numeric()
                        ->prefix('$')
                        ->default(0)
                        ->required(),

                    TextInput::make('price_yearly')
                        ->label('Yearly Price ($)')
                        ->numeric()
                        ->prefix('$')
                        ->default(0)
                        ->required(),
                ]),
            ]),

            Section::make('Limits')->schema([
                Grid::make(3)->schema([
                    TextInput::make('max_properties')
                        ->numeric()
                        ->default(-1)
                        ->helperText('-1 = unlimited')
                        ->required(),

                    TextInput::make('max_units')
                        ->numeric()
                        ->default(-1)
                        ->helperText('-1 = unlimited')
                        ->required(),

                    TextInput::make('max_users')
                        ->numeric()
                        ->default(-1)
                        ->helperText('-1 = unlimited')
                        ->required(),
                ]),

                KeyValue::make('limits')
                    ->keyLabel('Feature')
                    ->valueLabel('Value')
                    ->helperText('Optional extra feature flags (e.g. reports → true)'),
            ]),

            Section::make('Status')->schema([
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
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

                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price_monthly')
                    ->label('Monthly')
                    ->money('usd')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_yearly')
                    ->label('Yearly')
                    ->money('usd')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            'index'  => \App\Filament\Resources\Central\PlanResource\Pages\ListPlans::route('/'),
            'create' => \App\Filament\Resources\Central\PlanResource\Pages\CreatePlan::route('/create'),
            'edit'   => \App\Filament\Resources\Central\PlanResource\Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
