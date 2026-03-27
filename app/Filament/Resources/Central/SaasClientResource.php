<?php

namespace App\Filament\Resources\Central;

use App\Models\SaasClient;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;   
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Filament\Resources\Central\SaasClientResource\Pages;

class SaasClientResource extends Resource
{
    protected static ?string $model = SaasClient::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home-modern';

    protected static \UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('domain')
                            ->required()
                            ->label('Subdomain / Domain')
                            ->placeholder('client.localhost'),

                        Select::make('plan_id')
                            ->relationship('plan', 'name')
                            ->required()
                            ->preload(),

                        Select::make('status')
                            ->options([
                                'trial'     => 'Trial',
                                'active'    => 'Active',
                                'suspended' => 'Suspended',
                            ])
                            ->default('trial')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('domains.domain')
                    ->label('Domain')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSaasClients::route('/'),
            'create' => Pages\CreateSaasClient::route('/create'),
            'edit'   => Pages\EditSaasClient::route('/{record}/edit'),
        ];
    }
}