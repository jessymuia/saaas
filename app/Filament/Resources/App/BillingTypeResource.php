<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\RefBillingTypeExporter;
use App\Filament\Resources\App\BillingTypeResource\Pages;
use App\Models\RefBillingType;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;

class BillingTypeResource extends Resource
{
    protected static ?string $model = RefBillingType::class;

    protected static ?string $modelLabel = 'Billing Type';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::REFERENCES_NAVIGATION_GROUP;

    // Reference table — no saas_client_id scope needed

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('type')->required()->maxLength(255),
            Forms\Components\TextInput::make('due_day')->required()->numeric()->minValue(1)->maxValue(28)->hint('Due date of the month'),
            Forms\Components\TextInput::make('frequency_months')->label('Frequency')->required()->numeric()->minValue(1)->maxValue(12)->hint('Frequency of billing (months)'),
            Forms\Components\TextInput::make('description')->maxLength(65535),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('due_day')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('frequency_months')->label('Frequency (months)')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->sortable()->searchable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->headerActions([
                ExportAction::make()->exporter(RefBillingTypeExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(RefBillingTypeExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBillingTypes::route('/'),
            'create' => Pages\CreateBillingType::route('/create'),
            'view'   => Pages\ViewBillingType::route('/{record}'),
            'edit'   => Pages\EditBillingType::route('/{record}/edit'),
        ];
    }
}
