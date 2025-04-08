<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ManualInvoicesExporter;
use App\Filament\Resources\ManualInvoicesResource\Pages;
use App\Filament\Resources\ManualInvoicesResource\RelationManagers;
use App\Models\Client;
use App\Models\ManualInvoices;
use App\Models\PropertyOwners;
use App\Models\Tenant;
use App\Utils\AppUtils;
use Filament\Actions\ViewAction;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CompanyDetails;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Utils\AppPermissions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManualInvoicesResource extends Resource
{
    protected static ?string $model = ManualInvoices::class;

    protected static ?string $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Radio::make('recipient_type')
                    ->label('Recipient Type')
                    ->options([
                        'property_owner' => 'Property Owner',
                        'client' => 'Client',
                        'tenant' => 'Tenant',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        $recipientType = $get('recipient_type');

                        if ($recipientType === 'property_owner') {
                            $set('client_id', null);
                            $set('tenant_id', null);
                        } else if($recipientType === 'client'){
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
                    ->options(PropertyOwners::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->hidden(function (Forms\Get $get) {
                        return $get('recipient_type') === 'client' || $get('recipient_type') === 'tenant';
                    })
                    ->required(function (Forms\Get $get) {
                        return $get('recipient_type') === 'property_owner';
                    }),
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->options(Client::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->hidden(function (Forms\Get $get) {
                        return $get('recipient_type') === 'property_owner' || $get('recipient_type') === 'tenant';
                    })
                    ->required(function (Forms\Get $get) {
                        return $get('recipient_type') === 'client';
                    }),
                Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(Tenant::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->hidden(function (Forms\Get $get) {
                        return $get('recipient_type') === 'property_owner' || $get('recipient_type') === 'client';
                    })
                    ->required(function (Forms\Get $get) {
                        return $get('recipient_type') === 'tenant';
                    }),
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
                    ->hiddenOn(['create'])
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(ManualInvoices::accessibleByUser(auth()->user()))
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('propertyOwner.name')
                    ->label('Property Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_for_month')
                    ->searchable()
                    ->sortable()
                    ->date('F jS, Y'),
                Tables\Columns\TextColumn::make('invoice_due_date')
                    ->searchable()
                    ->sortable()
                    ->date('F jS, Y'),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean()
                    ->label('Confirmed?')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_generated')
                    ->boolean()
                    ->label('Generated?')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comments')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('View Invoice')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn (ManualInvoices $invoice) => !$invoice->is_generated)
                    ->url(function (ManualInvoices $invoice) {
                        if (!$invoice->is_generated) {
                            return route('preview.manual-invoice',['invoice'=>null]);
                        }
                        $fileName = str_replace('manual_invoices/','',$invoice->document_url);
                        return route('preview.manual-invoice',['invoice'=>$fileName]);
                    }),
                Tables\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_MANUAL_INVOICE_PDF))
                    ->action(function (ManualInvoices $record) {
                        try {
                            // Load the invoice with its relationships
                            $invoice = $record->load([
                                'propertyOwner',
                                'client',
                                'tenant',
                                'manualInvoiceItems',
                                'invoicePayments',
                                'creditNote'
                            ]);

                            $company = CompanyDetails::latest()->first();
                            if (!$company) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Company details not found')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Calculate totals
                            $subTotal = $invoice->manualInvoiceItems->sum('amount');
                            $vatTotal = $invoice->manualInvoiceItems->sum('vat');
                            $total = $invoice->manualInvoiceItems->sum('total_amount');
                            $creditNotes = $invoice->creditNote->sum('amount_credited');
                            $payments = $invoice->invoicePayments->sum('amount');
                            $balance = $total - $creditNotes - $payments;

                            // Get recipient details
                            $recipientName = $invoice->property_owner_id ? $invoice->propertyOwner->name :
                                           ($invoice->client_id ? $invoice->client->name :
                                           ($invoice->tenant_id ? $invoice->tenant->name : 'N/A'));

                            $recipientAddress = $invoice->property_owner_id ? $invoice->propertyOwner->address :
                                              ($invoice->client_id ? $invoice->client->address :
                                              ($invoice->tenant_id ? $invoice->tenant->address : 'N/A'));

                            $data = [
                                'invoice' => $invoice,
                                'company' => $company,
                                'recipientName' => $recipientName,
                                'recipientAddress' => $recipientAddress,
                                'subTotal' => $subTotal,
                                'vatTotal' => $vatTotal,
                                'total' => $total,
                                'creditNotes' => $creditNotes,
                                'payments' => $payments,
                                'balance' => $balance,
                                'timestamp' => now()->format('Y-m-d H:i:s'),
                                // Convert company logo to base64 if it exists
                                'logoData' => $company->logo ? base64_encode(file_get_contents(storage_path('app/public/' . $company->logo))) : null,
                                'logoExtension' => $company->logo ? pathinfo(storage_path('app/public/' . $company->logo), PATHINFO_EXTENSION) : null,
                            ];

                            // Configure PDF
                            $pdf = Pdf::loadView('pdfs.manual-invoices-details', $data);
                            $pdf->setPaper('A4', 'portrait');

                            // Set additional PDF options
                            $pdf->setOption('isPhpEnabled', true);
                            $pdf->setOption('isRemoteEnabled', true);
                            $pdf->setOption('isHtml5ParserEnabled', true);

                            return response()->streamDownload(
                                function () use ($pdf) {
                                    echo $pdf->output();
                                },
                                "manual-invoices-{$invoice->id}-details.pdf",
                                [
                                    'Content-Type' => 'application/pdf',
                                    'Content-Disposition' => 'attachment'
                                ]
                            );

                        } catch (\Exception $e) {
                            \Log::error('PDF Generation Error:', [
                                'error' => $e->getMessage(),
                                'invoice_id' => $record->id,
                                'stack_trace' => $e->getTraceAsString(),
                                'file' => $e->getFile(),
                                'line' => $e->getLine(),
                            ]);

                            Notification::make()
                                ->title('Error generating PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            \Log::error('PDF Generation Error:', [
                                'error' => $e->getMessage(),
                                'invoice_id' => $record->id,
                                'stack_trace' => $e->getTraceAsString()
                            ]);
                        }
                    })
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ManualInvoicesExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(ManualInvoicesExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                    ->fileDisk('local')
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\ManualInvoiceRelationManager::class,
            RelationManagers\InvoicePaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManualInvoices::route('/'),
            'create' => Pages\CreateManualInvoices::route('/create'),
            'edit' => Pages\EditManualInvoices::route('/{record}/edit'),
            'view' => Pages\ViewManualInvoices::route('/{record}'),
        ];
    }
}
