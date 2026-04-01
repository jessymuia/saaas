<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\ManualInvoicesExporter;
use App\Filament\Resources\App\ManualInvoicesResource\Pages;
use App\Filament\Resources\App\ManualInvoicesResource\RelationManagers;
use App\Models\Client;
use App\Models\ManualInvoices;
use App\Models\PropertyOwners;
use App\Models\Tenant;
use App\Utils\AppUtils;
use App\Utils\AppPermissions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManualInvoicesResource extends Resource
{
    protected static ?string $model = ManualInvoices::class;
    protected static bool $isScopedToTenant = false;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Radio::make('recipient_type')
                ->label('Recipient Type')
                ->options([
                    'property_owner' => 'Property Owner',
                    'client'         => 'Client',
                    'tenant'         => 'Tenant',
                ])
                ->live()
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    $type = $get('recipient_type');
                    if ($type === 'property_owner') {
                        $set('client_id', null);
                        $set('tenant_id', null);
                    } elseif ($type === 'client') {
                        $set('property_owner_id', null);
                        $set('tenant_id', null);
                    } else {
                        $set('property_owner_id', null);
                        $set('client_id', null);
                    }
                })
                ->required()
                ->columnSpan(2)
                ->default('property_owner'),

            Forms\Components\Select::make('property_owner_id')
                ->label('Property Owner')
                ->options(fn () => PropertyOwners::where('saas_client_id', filament()->getTenant()?->id)->pluck('name', 'id'))
                ->live()
                ->hidden(fn (Forms\Get $get) => in_array($get('recipient_type'), ['client', 'tenant']))
                ->required(fn (Forms\Get $get) => $get('recipient_type') === 'property_owner'),

            Forms\Components\Select::make('client_id')
                ->label('Client')
                ->options(fn () => Client::where('saas_client_id', filament()->getTenant()?->id)->pluck('name', 'id'))
                ->live()
                ->hidden(fn (Forms\Get $get) => in_array($get('recipient_type'), ['property_owner', 'tenant']))
                ->required(fn (Forms\Get $get) => $get('recipient_type') === 'client'),

            Forms\Components\Select::make('tenant_id')
                ->label('Tenant')
                ->options(fn () => Tenant::where('saas_client_id', filament()->getTenant()?->id)->pluck('name', 'id'))
                ->live()
                ->hidden(fn (Forms\Get $get) => in_array($get('recipient_type'), ['property_owner', 'client']))
                ->required(fn (Forms\Get $get) => $get('recipient_type') === 'tenant'),

            Forms\Components\DatePicker::make('invoice_for_month')
                ->label('Invoice For Month')
                ->required(),

            Forms\Components\DatePicker::make('invoice_due_date')
                ->label('Invoice Due Date')
                ->required(),

            Forms\Components\Textarea::make('comments')
                ->label('Comments')
                ->nullable()
                ->columnSpan(2),

            Forms\Components\Toggle::make('is_confirmed')
                ->label('Is Confirmed?')
                ->default(false)
                ->hiddenOn('create')
                ->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('propertyOwner.name')->label('Property Owner')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')->label('Tenant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('invoice_for_month')->date('F j, Y')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('invoice_due_date')->date('F j, Y')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_confirmed')->boolean()->label('Confirmed?')->sortable(),
                Tables\Columns\IconColumn::make('is_generated')->boolean()->label('Generated?')->sortable(),
                Tables\Columns\TextColumn::make('comments')->searchable()->sortable()->limit(30),
                Tables\Columns\TextColumn::make('createdBy.name')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('view_document')
                    ->label('View Invoice')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn (ManualInvoices $record) => !$record->is_generated)
                    ->url(function (ManualInvoices $record) {
                        if (!$record->is_generated) {
                            return '#';
                        }
                        $fileName = str_replace('manual-invoices/', '', $record->document_url);
                        return route('preview.manual-invoice', ['manualInvoice' => $fileName]);
                    }),
                \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(ManualInvoicesExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()->exporter(ManualInvoicesExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ManualInvoiceItemsRelationManager::class,
            RelationManagers\ManualInvoicePaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListManualInvoices::route('/'),
            'create' => Pages\CreateManualInvoice::route('/create'),
            'view'   => Pages\ViewManualInvoice::route('/{record}'),
            'edit'   => Pages\EditManualInvoice::route('/{record}/edit'),
        ];
    }
}
