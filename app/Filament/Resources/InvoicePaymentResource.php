<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoicePaymentResource\Pages;
use App\Filament\Resources\InvoicePaymentResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\InvoicePayment;
use App\Models\TenancyAgreement;
use App\Models\Unit;
use App\Rules\CheckPaidAmountDoesNotExceedAmountDue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class InvoicePaymentResource extends Resource
{
    protected static ?string $model = InvoicePayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_id')
                    ->label('Invoice ID')
                    ->reactive()
                    ->afterStateUpdated(function (Get $get,Set $set) {
                        $invoice = Invoice::find($get('invoice_id'));

                        $set('property_name',($invoice->tenancyAgreement->unit->property->name) ?? '');
                        $set('tenant_name',($invoice->tenancyAgreement->tenant->name) ?? '');
                        $set('total_due',number_format($invoice->totalDue(),2));
                    })
                    ->required(),
                Forms\Components\TextInput::make('total_due')
                    ->label('Total Due')
                    ->reactive()
                    ->hiddenOn(['edit','view'])
                    ->disabled(),
                Forms\Components\TextInput::make('property_name')
                    ->label('Property Name')
                    ->afterStateHydrated(function (Get $get,Set $set) {
                        $invoice = Invoice::find($get('invoice_id'));

                        $set('property_name',($invoice->tenancyAgreement->unit->property->name) ?? '');
                    })
                    ->reactive()
                    ->disabled(),
                Forms\Components\TextInput::make('tenant_name')
                    ->label('Tenant Name')
                    ->afterStateHydrated(function (Get $get,Set $set) {
                        $invoice = Invoice::find($get('invoice_id'));

                        $set('tenant_name',($invoice->tenancyAgreement->tenant->name) ?? '');
                    })
                    ->reactive()
                    ->disabled(),
                Forms\Components\Select::make('payment_type_id')
                    ->required()
                    ->relationship('paymentType', 'type'),
                Forms\Components\DateTimePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->rule(fn (Get $get) => new CheckPaidAmountDoesNotExceedAmountDue($get('invoice_id'))),
                Forms\Components\TextInput::make('paid_by')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_reference')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Additional Information')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_id')
                    ->label('Invoice ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.property.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.unit.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.tenancyAgreement.tenant.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentType.type')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_reference')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean()
                    ->label('Confirmed?'),
                Tables\Columns\TextColumn::make('document_generated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_generated_by.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
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
                Tables\Actions\EditAction::make()
                    ->visible(function (InvoicePayment $record) { // only visible if receipt has been confirmed
                        return !$record->is_confirmed;
                    }),
                // action to preview the receipt document
                Tables\Actions\Action::make('preview-receipt')
                    ->label('Preview Receipt')
                    ->icon('heroicon-o-document-text')
                    ->action(function (InvoicePayment $record) {
                        // get the invoice payment
                        $filename = str_replace('invoice_payments/', '', $record->document_path);
                        // preview invoice payment document
                        return redirect()
                            ->route('preview.receipt', ['receipt' => $filename]);
                    })
                    ->disabled(function (InvoicePayment $record) { // only visible if receipt has been confirmed
                        return strtotime($record->document_generated_at) === false;
                    }),

                // action to send the receipt document
                Tables\Actions\Action::make('send-receipt')
                    ->label('Send Receipt')
                    ->icon('heroicon-o-envelope')
                    ->action(function (InvoicePayment $record) {
                        // send the invoice payment mail
                        $record->sendInvoicePaymentEmail();
                    })
                    ->disabled(function (InvoicePayment $record) { // only visible if receipt has been confirmed
                        return strtotime($record->document_generated_at) === false
                            || strtotime($record->document_sent_at) !== false;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make()
//                        ->requiresConfirmation('Are you sure you want to delete the selected records?'),
                ]),
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
            'index' => Pages\ListInvoicePayments::route('/'),
            'create' => Pages\CreateInvoicePayment::route('/create'),
            'view' => Pages\ViewInvoicePayment::route('/{record}'),
            'edit' => Pages\EditInvoicePayment::route('/{record}/edit'),
        ];
    }
}
