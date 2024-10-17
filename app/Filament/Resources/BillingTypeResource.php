<?php

namespace App\Filament\Resources;

use App\Filament\Exports\RefBillingTypeExporter;
use App\Filament\Resources\BillingTypeResource\Pages;
use App\Filament\Resources\BillingTypeResource\RelationManagers;
use App\Models\BillingType;
use App\Models\RefBillingType;
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

class BillingTypeResource extends Resource
{
    protected static ?string $model = RefBillingType::class;

    protected static ?string $modelLabel = 'Billing Type';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = AppUtils::REFERENCES_NAVIGATION_GROUP;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('due_day')
                    ->required()
                    ->maxLength(2)
                    ->minValue(1)
                    ->maxValue(28)
                    ->hint('Due date of the month'),
                Forms\Components\TextInput::make('frequency_months')
                    ->label('Frequency')
                    ->required()
                    ->integer()
                    ->maxLength(2)
                    ->minValue(1)
                    ->maxValue(12)
                    ->hint('Frequency of billing(months)'),
                Forms\Components\TextInput::make('description')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_day')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency_months')
                    ->label('Frequency (months)')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(RefBillingTypeExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(RefBillingTypeExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])

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
            'index' => Pages\ListBillingTypes::route('/'),
            'create' => Pages\CreateBillingType::route('/create'),
            'view' => Pages\ViewBillingType::route('/{record}'),
            'edit' => Pages\EditBillingType::route('/{record}/edit'),
        ];
    }
}
