<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\InvoiceExporter;
use App\Filament\Resources\App\InvoiceResource\Pages;
use App\Filament\Resources\App\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Utils\AppPermissions;
use App\Utils\AppUtils;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CompanyDetails;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static UnitEnum|string|null $navigationGroup = 'Accounting';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $recordTitleAttribute = 'Invoice';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('comments')->maxLength(1000),
            Forms\Components\DatePicker::make('issue_date')->disabled()->readOnly(),
            Forms\Components\DatePicker::make('created_at')->disabled()->readOnly()->required(),
            Forms\Components\Toggle::make('is_confirmed')->label('Confirmed')->required(),
            Forms\Components\Toggle::make('is_generated')->label('Doc Generated')->disabled()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.property.name')->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('unpaid_amount')->numeric()->sortable(),
                Tables\Columns\IconColumn::make('is_confirmed')->boolean()->sortable()->label('Confirmed'),
                Tables\Columns\IconColumn::make('is_generated')->boolean()->label('Doc Generated'),
                Tables\Columns\TextColumn::make('comments')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('issue_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('invoice_for_month')->date('F, Y')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                Tables\Actions\Action::make('View Invoice')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn (Invoice $invoice) => !$invoice->is_generated)
                    ->url(function (Invoice $invoice) {
                        if (!$invoice->is_generated) {
                            return route('preview.invoice', ['invoice' => null]);
                        }
                        $fileName = str_replace('invoices/', '', $invoice->document_url);
                        return route('preview.invoice', ['invoice' => $fileName]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->mutateFormDataUsing(fn ($data) => ['deleted_by' => auth()->user()->id]),
            ])
            ->headerActions([
                ExportAction::make()->exporter(InvoiceExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                ExportBulkAction::make()->exporter(InvoiceExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TenancyBillsRelationManager::class,
            RelationManagers\CreditNoteRelationManager::class,
            RelationManagers\InvoicePaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
