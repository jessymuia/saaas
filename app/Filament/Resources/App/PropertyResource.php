<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\PropertyExporter;
use App\Filament\Resources\App\PropertyResource\Pages;
use App\Filament\Resources\App\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Models\CompanyDetails;
use App\Utils\AppPermissions;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static UnitEnum|string|null $navigationGroup = 'Tenancy Management';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

   
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('address')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('number_of_units')
                ->integer()
                ->minValue(1)
                ->required(),

            Forms\Components\Select::make('property_type_id')
                ->label('Property Type')
                ->required()
                ->reactive()
                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                    $propertyTypeId = $get('property_type_id');
                    \Log::info("property_type_id: $propertyTypeId");
                    $isVatable = $propertyTypeId == 1;
                    $set('is_vatable', $isVatable);
                })
                ->relationship('propertyType', 'type'),

            Forms\Components\Checkbox::make('is_vatable')
                ->label('Is Vatable? (is the property VATable?)')
                ->reactive(),

            Forms\Components\Textarea::make('description')
                ->maxLength(65535)
                ->columnSpanFull(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('propertyType.type')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('number_of_units')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_vatable')
                    ->boolean(),

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
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),

                \Filament\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_PROPERTY_PDF))
                    ->action(function ($record) {
                        $property = $record->load([
                            'propertyType',
                            'units' => fn ($q) => $q->orderBy('name'),
                            'utilities',
                            'propertyServices',
                            'propertyPaymentDetails' => fn ($q) => $q->where('status', true),
                            'propertyOwners'         => fn ($q) => $q->where('status', true),
                        ]);

                        $company = CompanyDetails::latest()->first();

                        if (!$property->propertyPaymentDetails) {
                            $property->setRelation('propertyPaymentDetails', collect([]));
                        }

                        if (!$company) {
                            throw new \Exception('Company details not found. Please set up company details first.');
                        }

                        $pdf = Pdf::loadView('pdfs.property-details', [
                            'property'           => $property,
                            'timestamp'          => now()->format('Y-m-d H:i:s'),
                            'company'            => $company,
                            'logoUrl'            => $company->logo_url,
                            'companyLocation'    => $company->location,
                            'companyAddress'     => $company->address,
                            'companyPhoneNumber' => $company->phone_number,
                            'companyEmail'       => $company->email,
                        ]);

                        $pdf->setPaper('A4', 'portrait');

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "{$property->name}-{$property->id}-details.pdf"
                        );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PropertyExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()
                    ->exporter(PropertyExporter::class)
                    ->formats([ExportFormat::Csv])
                    ->fileDisk('local'),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public static function getRelations(): array
    {
        return [
           // RelationManagers\UnitsRelationManager::class,
           // RelationManagers\TenancyAgreementsRelationManager::class,
            //RelationManagers\UtilitiesRelationManager::class,
            //RelationManagers\PropertyServicesRelationManager::class,
            //RelationManagers\MeterReadingsRelationManager::class,
            //RelationManagers\VacationNoticesRelationManager::class,
            //RelationManagers\UnitsOccupiedByRelationManager::class,
            //RelationManagers\PaymentDetailsRelationManager::class,
            // RelationManagers\EscalationRatesAndAmountsLogsRelationManager::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view'   => Pages\ViewProperty::route('/{record}'),
            'edit'   => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
