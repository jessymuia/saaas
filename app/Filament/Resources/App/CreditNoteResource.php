<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\CreditNoteExporter;
use App\Filament\Resources\App\CreditNoteResource\Pages;
use App\Models\CreditNote;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CreditNoteResource extends Resource
{
    protected static ?string $model = CreditNote::class;
    protected static bool $isScopedToTenant = false;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-refund';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->label('Title')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('amount_credited')
                ->label('Amount Credited')
                ->required()
                ->minValue(1)
                ->numeric()
                ->step(0.01),
            Forms\Components\Textarea::make('reason_for_issuance')
                ->label('Reason for Issuance')
                ->required()
                ->rows(4)
                ->maxLength(1000),
            Forms\Components\Textarea::make('notes')
                ->label('Notes')
                ->rows(4)
                ->maxLength(1000),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('amount_credited')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('issue_date')->date('F j, Y')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('reason_for_issuance')->limit(30)->sortable()->searchable(),
                Tables\Columns\TextColumn::make('notes')->limit(30)->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confirmed')->label('Confirmed')->boolean(),
                Tables\Columns\IconColumn::make('is_document_generated')->label('Doc Generated')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->date('F j, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->date('F j, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('view_document')
                    ->label('View Document')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn (CreditNote $record) => !$record->is_document_generated)
                    ->url(function (CreditNote $creditNote) {
                        if (!$creditNote->is_document_generated) {
                            return route('preview.credit-note', ['creditNote' => null]);
                        }
                        $fileName = str_replace('credit-notes/', '', $creditNote->document_url);
                        return route('preview.credit-note', ['creditNote' => $fileName]);
                    }),
                \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(CreditNoteExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()->exporter(CreditNoteExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCreditNotes::route('/'),
            'create' => Pages\CreateCreditNote::route('/create'),
            'view'   => Pages\ViewCreditNote::route('/{record}'),
            'edit'   => Pages\EditCreditNote::route('/{record}/edit'),
        ];
    }
}
