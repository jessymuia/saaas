<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PropertyOwnersExporter;
use App\Filament\Resources\PropertyOwnersResource\Pages;
use App\Filament\Resources\PropertyOwnersResource\RelationManagers;
use App\Models\Property;
use App\Models\PropertyOwners;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CompanyDetails;
use App\Models\ManualInvoices;
use App\Models\InvoicePayment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyOwnersResource extends Resource
{
    protected static ?string $model = PropertyOwners::class;

    protected static ?string $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('property_id')
                    ->label('Property')
                    ->required()
                    ->options(function () use ($form) {
                        return Property::query()
                            ->where(function ($query) use ($form){
                                if ($form->getOperation() == "edit"){
                                    \Log::info("Edit operation");
                                    \Log::info($form->getRecord()->property_id);
                                    $query->whereDoesntHave('propertyOwners')
                                        ->orWhere('id', $form->getRecord()->property_id);
                                }else{
                                    \Log::info("Another operation");
                                    $query->whereDoesntHave('propertyOwners');
                                }
                            })
                            ->pluck('name', 'id');
                    }),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->nullable()
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('balance_carried_forward')
                    ->required()
                    ->numeric()
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.name')
                    ->searchable()
                    ->sortable()
                    ->label('Property'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_carried_forward')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('generate-statement-of-account')
                    ->label('Generate Statement of Account')
                    ->action(fn(PropertyOwners $record)=>$record->generateStatementOfAccountVersionTwo()),
//                    ->action(fn(PropertyOwners $record)=>$record->generateStatementOfAccount()),
                Tables\Actions\Action::make('generate-invoice-for-balance-carried-forward')
                    ->label('Bill Balance Carried Forward')
                    ->icon("heroicon-m-document-check")
                    ->disabled(fn(PropertyOwners $record) => $record->has_invoice_for_balance_carried_forward)
                    ->requiresConfirmation(fn($record) => 'Are you sure you would like to create an invoice for the balance carried forward for this property owner?')
                    ->action(function (PropertyOwners $record) {
                        $response = $record->createInvoiceForBalanceCarriedForward();

                        if ($response["status"] == -1){
                            // pop up a toast message
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body($response["message"])
                                ->duration(5000)
                                ->icon('heroicon-o-x-circle')
                                ->send();
                        }
                        if ($response["status"] == 1){
                            // pop up a toast message
                            Notification::make()
                                ->title('Success')
                                ->success()
                                ->body($response["message"])
                                ->duration(5000)
                                ->icon('heroicon-o-check-circle')
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn ($data) => [
                        'updated_by' => auth()->user()->id,
                    ]),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->mutateFormDataUsing(fn ($data) => [
                        'deleted_by' => auth()->user()->id,
                    ]),
                    Tables\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->action(function (PropertyOwners $record) {
                        try {
                            // Load the property owner with its relationships
                            $propertyOwner = $record->load([
                                'property'
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
                
                            // Get all manual invoices for this property owner
                            $invoices = ManualInvoices::where('property_owner_id', $propertyOwner->id)
                                ->where('is_confirmed', true)
                                ->get();
                
                            // Calculate totals
                            $totalInvoiced = $invoices->sum('amount');
                            $totalPaid = InvoicePayment::where('property_owner_id', $propertyOwner->id)
                                ->where('is_confirmed', true)
                                ->sum('amount');
                            $balance = $totalInvoiced - $totalPaid + $propertyOwner->balance_carried_forward;
                
                            $data = [
                                'propertyOwner' => $propertyOwner,
                                'company' => $company,
                                'invoices' => $invoices,
                                'totalInvoiced' => $totalInvoiced,
                                'totalPaid' => $totalPaid,
                                'balance' => $balance,
                                'balanceCarriedForward' => $propertyOwner->balance_carried_forward,
                                'timestamp' => now()->format('Y-m-d H:i:s'),
                                'logoData' => $company->logo ? base64_encode(file_get_contents(storage_path('app/public/' . $company->logo))) : null,
                                'logoExtension' => $company->logo ? pathinfo(storage_path('app/public/' . $company->logo), PATHINFO_EXTENSION) : null,
                            ];
                
                            $pdf = Pdf::loadView('pdfs.property-owners-details', $data);
                            $pdf->setPaper('A4', 'portrait');
                            
                            // Set additional PDF options
                            $pdf->setOption('isPhpEnabled', true);
                            $pdf->setOption('isRemoteEnabled', true);
                            $pdf->setOption('isHtml5ParserEnabled', true);
                
                            return response()->streamDownload(
                                function () use ($pdf) {
                                    echo $pdf->output();
                                }, 
                                "property-owners-{$propertyOwner->id}-details.pdf", 
                                [
                                    'Content-Type' => 'application/pdf',
                                    'Content-Disposition' => 'attachment'
                                ]
                            );
                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error generating PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            
                            \Log::error('PDF Generation Error:', [
                                'error' => $e->getMessage(),
                                'property_owner_id' => $record->id,
                                'stack_trace' => $e->getTraceAsString()
                            ]);
                        }
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PropertyOwnersExporter::class)
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
                    ->exporter(PropertyOwnersExporter::class)
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyOwners::route('/'),
            'create' => Pages\CreatePropertyOwners::route('/create'),
            'edit' => Pages\EditPropertyOwners::route('/{record}/edit'),
        ];
    }
}
