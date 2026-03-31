<?php

namespace App\Filament\Resources\Central;

use App\Models\SaasClient;
use App\Models\User;
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
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Filament\Resources\Central\SaasClientResource\Pages;
use Filament\Notifications\Notification;

class SaasClientResource extends Resource
{
    protected static ?string $model = SaasClient::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home-modern';

    protected static \UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('domain')
                            ->required()
                            ->label('Subdomain / Domain')
                            ->placeholder('client.localhost'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Client Email'),

                        TextInput::make('contact_name')
                            ->label('Contact Person Name'),

                        TextInput::make('phone')
                            ->label('Phone Number'),

                        Select::make('plan_id')
                            ->relationship('plan', 'name')
                            ->required()
                            ->preload(),

                        Select::make('status')
                            ->options([
                                'trial' => 'Trial',
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                            ])
                            ->default('trial')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('domains.domain')
                    ->label('Domain')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'warning',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),

                Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (SaasClient $record) {
                        try {
                            $newPassword = Str::random(12);

                            $user = User::withoutGlobalScopes()
                                ->where('saas_client_id', $record->id)
                                ->first();

                            if (!$user) {
                                Notification::make()
                                    ->title('No admin user found')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            User::withoutGlobalScopes()
                                ->where('id', $user->id)
                                ->update([
                                    'password' => Hash::make($newPassword)
                                ]);

                            Notification::make()
                                ->title('Password Reset')
                                ->body("Email: {$user->email}\nPassword: {$newPassword}")
                                ->success()
                                ->persistent()
                                ->send();

                        } catch (\Throwable $e) {
                            Log::error('Password reset failed: ' . $e->getMessage());

                            Notification::make()
                                ->title('Password reset failed')
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSaasClients::route('/'),
            'create' => Pages\CreateSaasClient::route('/create'),
            'edit' => Pages\EditSaasClient::route('/{record}/edit'),
        ];
    }
}