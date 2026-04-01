<?php

namespace App\Filament\Resources\App\ManualInvoicesResource\RelationManagers;

use App\Filament\Exports\InvoicePaymentExporter;
use App\Models\InvoicePayment;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ExportBulkAction;

class ManualInvoicePaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'invoicePayments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('payment_type_id')
                ->required()
                ->relationship('paymentType', 'type'),
            Forms\Components\DateTimePicker::make('payment_date')->required(),
            Forms\Components\TextInput::make('amount')->required()->numeric(),
            Forms\Components\TextInput::make('paid_by')->required()->maxLength(255),
            Forms\Components\TextInput::make('payment_reference')->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->label('Additional Information')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('paymentType.type')->sortable(),
                Tables\Columns\TextColumn::make('payment_date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('amount')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('paid_by')->searchable(),
                Tables\Columns\TextColumn::make('payment_reference')->searchable(),
                Tables\Columns\IconColumn::make('is_confirmed')->boolean()->label('Confirmed?'),
                Tables\Columns\TextColumn::make('document_generated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (InvoicePayment $record) => !$record->is_confirmed),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()->exporter(InvoicePaymentExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }
}
