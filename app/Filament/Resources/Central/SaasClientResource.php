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
use Filament\Actions\Action;
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
                Section::make('Company Details')
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
                            ->placeholder('christopher.localhost'),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Client Email')
                            ->placeholder('admin@christopherproperty.com'),

                        TextInput::make('contact_name')
                            ->label('Contact Person Name')
                            ->placeholder('Christopher Doe'),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->placeholder('+254700000000'),

                        Select::make('plan_id')
                            ->relationship('plan', 'name')
                            ->required()
                            ->preload()
                            ->live(),

                        Select::make('status')
                            ->options([
                                'trial'     => 'Trial',
                                'active'    => 'Active',
                                'suspended' => 'Suspended',
                            ])
                            ->default('trial')
                            ->required()
                            ->live(),
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
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('domains.domain')
                    ->label('Domain')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'trial'     => 'warning',
                        'suspended' => 'danger',
                        default     => 'gray',
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
                        $newPassword = \Illuminate\Support\Str::random(12);
                        $user = \App\Models\User::where('saas_client_id', $record->id)->first();

                        if ($user) {
                            \Illuminate\Support\Facades\DB::statement("SET app.current_tenant_id = '{$record->id}'");
                            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($newPassword)]);

                            // Send email with new password
                            if ($record->email) {
                                $record->notify(new \App\Notifications\TenantWelcomeNotification(
                                    tenantName: $record->name,
                                    loginUrl: 'http://' . ($record->domains->first()->domain ?? $record->slug . '.localhost:8000') . '/app/login',
                                    email: $user->email,
                                    password: $newPassword,
                                ));
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Password Reset')
                                ->body("New password: {$newPassword}\nEmail: {$user->email}")
                                ->success()
                                ->persistent()
                                ->send();
                        }
                    }),
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