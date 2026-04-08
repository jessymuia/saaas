<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\TenancyAgreementExporter;
use App\Filament\Resources\App\TenancyAgreementTypeResource\Pages;
use App\Models\RefTenancyAgreementType;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;

class TenancyAgreementTypeResource extends Resource
{
    protected static ?string $model = RefTenancyAgreementType::class;
    protected static bool $isScopedToTenant = false;

    protected static ?string $modelLabel = 'Tenancy Agreement Type';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::REFERENCES_NAVIGATION_GROUP;

   

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('type')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->maxLength(65535)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->sortable()->searchable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([\Filament\Actions\ViewAction::make(), \Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()->requiresConfirmation()])
            ->headerActions([
                ExportAction::make()->exporter(TenancyAgreementExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(TenancyAgreementExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTenancyAgreementTypes::route('/'),
            'create' => Pages\CreateTenancyAgreementType::route('/create'),
            'view'   => Pages\ViewTenancyAgreementType::route('/{record}'),
            'edit'   => Pages\EditTenancyAgreementType::route('/{record}/edit'),
        ];
    }
}
