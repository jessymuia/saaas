<?php

namespace App\Filament\Resources\Central;

use App\Filament\Resources\Central\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static \UnitEnum|string|null $navigationGroup = 'SaaS Management';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Ticket Details')->schema([
                Forms\Components\Select::make('saas_client_id')
                    ->relationship('saasClient', 'name')
                    ->required(),
                Forms\Components\TextInput::make('subject')->required(),
                Forms\Components\Textarea::make('message')->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'open'     => 'Open',
                        'pending'  => 'Pending',
                        'resolved' => 'Resolved',
                    ])
                    ->default('open'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('saasClient.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('subject')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([EditAction::make()])                              
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),     
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit'   => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
