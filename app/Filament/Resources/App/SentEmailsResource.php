<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\SentEmailsExporter;
use App\Filament\Resources\App\SentEmailsResource\Pages;
use App\Models\SentEmails;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SentEmailsResource extends Resource
{
    protected static ?string $model = SentEmails::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::TENANCY_MANAGEMENT_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    /*
    |--------------------------------------------------------------------------
    | TENANT SCOPE — Phase 10.4
    |--------------------------------------------------------------------------
    */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('recipient_email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subject')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reference_id')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('delivery_status')
                    ->icon(fn (string $state): string => match ($state) {
                        'SENT'    => 'heroicon-o-check-circle',
                        'FAILED'  => 'heroicon-o-x-circle',
                        'PENDING' => 'heroicon-o-clock',
                        default   => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'SENT'    => 'success',
                        'FAILED'  => 'danger',
                        'PENDING' => 'warning',
                        default   => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('failure_reason')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([])
            ->headerActions([
                ExportAction::make()->exporter(SentEmailsExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(SentEmailsExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSentEmails::route('/'),
        ];
    }
}
