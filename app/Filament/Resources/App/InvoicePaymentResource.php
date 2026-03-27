<?php

namespace App\Filament\Resources\App;

use App\Filament\Resources\App\InvoicePaymentResource\Pages;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Rules\CheckPaidAmountDoesNotExceedAmountDue;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoicePaymentResource extends Resource
{
    protected static ?string $model = InvoicePayment::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

   
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('invoice_id')
                ->label('Invoice ID')
                ->reactive()
                ->afterStateUpdated(function (Get $get, Set $set) {
                    $invoice = Invoice::find($get('invoice_id'));
                    $set('property_name', $invoice->tenancyAgreement->unit->property->name ?? '');
                    $set('tenant_name', $invoice->tenancyAgreement->tenant->name ?? '');
                    $set('total_due', number_format($invoice->totalDue(), 2));
                })
                ->required(),
            Forms\Components\TextInput::make('total_due')->label('Total Due')->reactive()->hiddenOn(['edit', 'view'])->disabled(),
            Forms\Components\TextInput::make('property_name')
                ->label('Property Name')
                ->afterStateHydrated(function (Get $get, Set $set) {
                    $invoice = Invoice::find($get('invoice_id'));
                    $set('property_name', $invoice->tenancyAgreement->unit->property->name ?? '');
                })
                ->reactive()->disabled(),
            Forms\Components\TextInput::make('tenant_name')
                ->label('Tenant Name')
                ->afterStateHydrated(function (Get $get, Set $set) {
                    $invoice = Invoice::find($get('invoice_id'));
                    $set('tenant_name', $invoice->tenancyAgreement->tenant->name ?? '');
                })
                ->reactive()->disabled(),
            Forms\Components\Select::make('payment_type_id')->required()->relationship('paymentType', 'type'),
            Forms\Components\DateTimePicker::make('payment_date')->required(),
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->rule(fn (Get $get) => new CheckPaidAmountDoesNotExceedAmountDue($get('invoice_id'))),
            Forms\Components\TextInput::make('paid_by')->required()->maxLength(255),
            Forms\Components\TextInput::make('payment_reference')->maxLength(255),
            Forms\Components\Textarea::make('description')->label('Additional Information')->maxLength(65535)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_id')->label('Invoice ID')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.property.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.unit.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.tenant.name')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('paymentType.type')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('payment_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('amount')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('paid_by')->searchable(),
                Tables\Columns\TextColumn::make('receivedBy.name')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_reference')->sortable()->searchable(),
                Tables\Columns\IconColumn::make('status')->boolean()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confirmed')->boolean()->label('Confirmed?'),
                Tables\Columns\TextColumn::make('document_generated_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('document_generated_by.name')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (InvoicePayment $record) => !$record->is_confirmed),
                Tables\Actions\Action::make('preview-receipt')
                    ->label('Preview Receipt')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn (InvoicePayment $record) => !$record->document_generated_at)
                    ->action(function (InvoicePayment $record) {
                        $filename = str_replace('invoice_payments/', '', $record->document_path);
                        return redirect()->route('preview.receipt', ['receipt' => $filename]);
                    }),
                Tables\Actions\Action::make('send-receipt')
                    ->label('Send Receipt')
                    ->icon('heroicon-o-envelope')
                    ->disabled(fn (InvoicePayment $record) => !$record->document_generated_at || $record->document_sent_at)
                    ->action(fn (InvoicePayment $record) => $record->sendInvoicePaymentEmail()),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoicePayments::route('/'),
            'create' => Pages\CreateInvoicePayment::route('/create'),
            'view'   => Pages\ViewInvoicePayment::route('/{record}'),
            'edit'   => Pages\EditInvoicePayment::route('/{record}/edit'),
        ];
    }
}
