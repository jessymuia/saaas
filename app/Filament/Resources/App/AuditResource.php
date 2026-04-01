<?php

namespace App\Filament\Resources\App;

use App\Filament\Resources\App\AuditResource\Pages;
use App\Models\User;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Models\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;
    protected static bool $isScopedToTenant = false;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCESS_MANAGEMENT_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('user_type'),
            Forms\Components\TextInput::make('user_id'),
            Forms\Components\TextInput::make('event'),
            Forms\Components\TextInput::make('auditable_type'),
            Forms\Components\TextInput::make('auditable_id'),
            Forms\Components\Textarea::make('old_values')
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),
            Forms\Components\Textarea::make('new_values')
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT)),
            Forms\Components\TextInput::make('url'),
            Forms\Components\TextInput::make('ip_address'),
            Forms\Components\TextInput::make('user_agent'),
            Forms\Components\TextInput::make('tags'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->numeric()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user_type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->state(fn (Model $model) => $model->user_id ? User::find($model->user_id)?->name : null),
                Tables\Columns\TextColumn::make('event')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('auditable_type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('auditable_id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('old_values')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state) : '—')
                    ->limit(60)
                    ->tooltip(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : null),
                Tables\Columns\TextColumn::make('new_values')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state) : '—')
                    ->limit(60)
                    ->tooltip(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : null),
                Tables\Columns\TextColumn::make('url')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('ip_address')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user_agent')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tags')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([\Filament\Actions\ViewAction::make()]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
            'view'  => Pages\ViewAudit::route('/{record}'),
        ];
    }
}
