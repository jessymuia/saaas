<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TenantExporter;
use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use App\Models\CompanyDetails;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Utils\AppPermissions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationGroup = AppUtils::TENANCY_MANAGEMENT_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Tenant::accessibleByUser(auth()->user()))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
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
                Tables\Columns\TextColumn::make('deletedBy.name')
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
                Tables\Columns\TextColumn::make('deleted_at')
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
                Tables\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_TENANT_PDF))
                    ->action(function (Tenant $record) {
                        try {
                            ini_set('max_execution_time', 300);
                            
                            $tenant = $record->load([
                                'tenancyAgreements.tenancyBills'
                            ]);
                    
                            $company = CompanyDetails::latest()->first();
                            if (!$company) {
                                throw new \Exception('Company details not found. Please set up company details first.');
                            }
                    
                            $data = [
                                'tenant' => $tenant,
                                'company' => $company,
                                'bills' => $tenant->tenancyAgreements->flatMap->tenancyBills
                            ];
                    
                            $pdf = Pdf::loadView('pdfs.tenant-details', $data);
                            $pdf->setPaper('A4', 'portrait');
                    
                            $pdf->setOption('isPhpEnabled', true);
                            $pdf->setOption('isRemoteEnabled', true);
                            $pdf->setOption('isHtml5ParserEnabled', true);
                    
                            return response()->streamDownload(
                                function () use ($pdf) {
                                    echo $pdf->output();
                                },
                                "{$tenant->name}-{$tenant->id}-details.pdf",
                                [
                                    'Content-Type' => 'application/pdf',
                                    'Content-Disposition' => 'attachment'
                                ]
                            );
                    
                        } catch (\Exception $e) {
                            \Log::error('PDF Generation Error:', [
                                'error' => $e->getMessage(),
                                'tenant_id' => $record->id,
                                'stack_trace' => $e->getTraceAsString()
                            ]);
                            throw $e;
                        }
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(TenantExporter::class)
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
                    ->exporter(TenantExporter::class)
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
            RelationManagers\TenancyAgreementsRelationManager::class,
            RelationManagers\TenancyBillsRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\InvoicePaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
