<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SentEmailsExporter;
use App\Filament\Resources\SentEmailsResource\Pages;
use App\Filament\Resources\SentEmailsResource\RelationManagers;
use App\Models\SentEmails;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SentEmailsResource extends Resource
{
    protected static ?string $model = SentEmails::class;

    protected static ?string $navigationGroup = AppUtils::TENANCY_MANAGEMENT_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('recipient_email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('delivery_status')
                    ->icon(fn(string $state):string => match ($state) {
                        'SENT' => 'heroicon-o-check-circle',
                        'FAILED' => 'heroicon-o-x-circle',
                        'PENDING' => 'heroicon-o-clock',
                    })
                    ->color(fn(string $state):string => match ($state) {
                        'SENT' => 'text-green-600',
                        'FAILED' => 'text-red-600',
                        'PENDING' => 'text-yellow-600',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('failure_reason')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // action to resend email
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(SentEmailsExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(SentEmailsExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
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
            'index' => Pages\ListSentEmails::route('/'),
//            'create' => Pages\CreateSentEmails::route('/create'),
//            'edit' => Pages\EditSentEmails::route('/{record}/edit'),
//            'view' => Pages\ViewSentEmails::route('/{record}'),
        ];
    }
}
