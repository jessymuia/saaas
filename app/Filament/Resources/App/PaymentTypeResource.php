<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\RefPaymentTypeExporter;
use App\Filament\Resources\App\PaymentTypeResource\Pages;
use App\Models\RefPaymentType;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;

class PaymentTypeResource extends Resource
{
    protected static ?string $model = RefPaymentType::class;

    protected static ?string $modelLabel = 'Payment Type';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::REFERENCES_NAVIGATION_GROUP;

    // Reference table — no saas_client_id scope needed

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('type')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->maxLength(65535),
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
            ->actions([\Filament\Actions\ViewAction::make(), Tables\Actions\EditAction::make()])
            ->headerActions([
                ExportAction::make()->exporter(RefPaymentTypeExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(RefPaymentTypeExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPaymentTypes::route('/'),
            'create' => Pages\CreatePaymentType::route('/create'),
            'view'   => Pages\ViewPaymentType::route('/{record}'),
            'edit'   => Pages\EditPaymentType::route('/{record}/edit'),
        ];
    }
}
