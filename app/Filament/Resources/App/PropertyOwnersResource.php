<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\PropertyOwnersExporter;
use App\Filament\Resources\App\PropertyOwnersResource\Pages;
use App\Models\CompanyDetails;
use App\Models\InvoicePayment;
use App\Models\ManualInvoices;
use App\Models\Property;
use App\Models\PropertyOwners;
use App\Utils\AppPermissions;
use App\Utils\AppUtils;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyOwnersResource extends Resource
{
    protected static ?string $model = PropertyOwners::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        $tenantId = filament()->getTenant()?->id;

        return $schema->schema([
            Forms\Components\Select::make('property_id')
                ->label('Property')
                ->required()
                ->options(function () use ($tenantId, $schema) {
                    return Property::query()
                        ->where('saas_client_id', $tenantId)
                        ->where(function ($query) use ($schema) {
                            if ($schema->getOperation() === 'edit') {
                                $query->whereDoesntHave('propertyOwners')
                                    ->orWhere('id', $schema->getRecord()->property_id);
                            } else {
                                $query->whereDoesntHave('propertyOwners');
                            }
                        })
                        ->pluck('name', 'id');
                }),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('email')->nullable()->email()->maxLength(255),
            Forms\Components\TextInput::make('phone_number')->nullable()->maxLength(255),
            Forms\Components\TextInput::make('address')->required()->maxLength(255),
            Forms\Components\TextInput::make('tax_pin')->nullable()->maxLength(50)->label('Tax PIN'),
            Forms\Components\TextInput::make('balance_carried_forward')->required()->numeric()->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('property.name')->searchable()->sortable()->label('Property'),
                Tables\Columns\TextColumn::make('address')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tax_pin')->searchable()->sortable()->label('Tax PIN'),
                Tables\Columns\TextColumn::make('balance_carried_forward')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\Action::make('generate-statement-of-account')
                    ->label('Generate Statement of Account')
                    ->action(fn (PropertyOwners $record) => $record->generateStatementOfAccountVersionTwo()),
                \Filament\Actions\Action::make('generate-invoice-for-balance-carried-forward')
                    ->label('Bill Balance Carried Forward')
                    ->icon('heroicon-m-document-check')
                    ->disabled(fn (PropertyOwners $record) => $record->has_invoice_for_balance_carried_forward)
                    ->requiresConfirmation()
                    ->action(function (PropertyOwners $record) {
                        $response = $record->createInvoiceForBalanceCarriedForward();
                        $type = $response['status'] == 1 ? 'success' : 'danger';
                        $title = $response['status'] == 1 ? 'Success' : 'Error';
                        Notification::make()->title($title)->{$type}()->body($response['message'])->duration(5000)->send();
                    }),
                \Filament\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn ($data) => array_merge($data, ['updated_by' => auth()->id()])),
                \Filament\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->mutateFormDataUsing(fn ($data) => array_merge($data, ['deleted_by' => auth()->id()])),
                \Filament\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_PROPERTY_OWNER_PDF))
                    ->action(function (PropertyOwners $record) {
                        try {
                            $propertyOwner = $record->load(['property']);
                            $company       = CompanyDetails::latest()->first();
                            if (!$company) {
                                Notification::make()->title('Error')->body('Company details not found')->danger()->send();
                                return;
                            }
                            $invoices     = ManualInvoices::where('property_owner_id', $propertyOwner->id)->where('is_confirmed', true)->get();
                            $totalInvoiced = $invoices->sum('amount');
                            $totalPaid    = InvoicePayment::where('property_owner_id', $propertyOwner->id)->where('is_confirmed', true)->sum('amount');
                            $balance      = $totalInvoiced - $totalPaid + $propertyOwner->balance_carried_forward;
                            $pdf = Pdf::loadView('pdfs.property-owners-details', [
                                'propertyOwner'        => $propertyOwner,
                                'company'              => $company,
                                'invoices'             => $invoices,
                                'totalInvoiced'        => $totalInvoiced,
                                'totalPaid'            => $totalPaid,
                                'balance'              => $balance,
                                'balanceCarriedForward'=> $propertyOwner->balance_carried_forward,
                                'timestamp'            => now()->format('Y-m-d H:i:s'),
                                'logoUrl'              => 'file://'.storage_path('app/public/'.$company->logo),
                            ]);
                            $pdf->setPaper('A4', 'portrait');
                            $pdf->setOption('isPhpEnabled', true);
                            $pdf->setOption('isRemoteEnabled', true);
                            $pdf->setOption('isHtml5ParserEnabled', true);
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                "{$propertyOwner->name}-{$propertyOwner->id}-details.pdf",
                                ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment']
                            );
                        } catch (\Exception $e) {
                            Notification::make()->title('Error generating PDF')->body($e->getMessage())->danger()->send();
                            \Log::error('PDF Generation Error:', ['error' => $e->getMessage(), 'property_owner_id' => $record->id]);
                        }
                    }),
            ])
            ->headerActions([
                ExportAction::make()->exporter(PropertyOwnersExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(PropertyOwnersExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPropertyOwners::route('/'),
            'create' => Pages\CreatePropertyOwners::route('/create'),
            'edit'   => Pages\EditPropertyOwners::route('/{record}/edit'),
        ];
    }
}
