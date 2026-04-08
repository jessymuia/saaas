<?php

namespace App\Filament\Resources\Central;

use App\Models\SaasClient;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Filament\Resources\Central\TenantUserResource\Pages;

class TenantUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static \UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    protected static ?string $navigationLabel = 'Tenant Users';

    protected static ?string $modelLabel = 'Tenant User';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Details')
                    ->schema([
                        Select::make('saas_client_id')
                            ->label('Tenant')
                            ->options(SaasClient::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live(),

                        TextInput::make('name')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->required(),

                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->label('Password'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Bypass RLS for super admin
                DB::statement('RESET app.current_tenant_id');
                return $query;
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('saasClient.name')
                    ->label('Tenant')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTenantUsers::route('/'),
            'create' => Pages\CreateTenantUser::route('/create'),
            'edit'   => Pages\EditTenantUser::route('/{record}/edit'),
        ];
    }
}