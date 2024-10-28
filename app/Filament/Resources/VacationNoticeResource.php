<?php

namespace App\Filament\Resources;

use App\Filament\Exports\VacationNoticesExporter;
use App\Filament\Resources\VacationNoticeResource\Pages;
use App\Filament\Resources\VacationNoticeResource\RelationManagers;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\VacationNotice;
use App\Models\VacationNotices;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VacationNoticeResource extends Resource
{
    protected static ?string $model = VacationNotices::class;

    protected static ?string $navigationGroup = AppUtils::TENANCY_MANAGEMENT_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('property_id')
                    ->options(Property::query()->pluck('name', 'id'))
                    ->label('Property')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('unit_id')
                    ->required()
                    ->options(function (Forms\Get $get) {
                        // check if property_id is set
                        if ($get('property_id') != null && $get('property_id') != "") {
                            // get units for property
                            return \App\Models\Unit::query()
                                ->where('property_id', $get('property_id'))
                                ->pluck('name', 'id');
                        }else{
                            return [];
                        }
                    })
                    ->label('Unit')
                    ->reactive(),
                Forms\Components\Select::make('tenant_id')
                    ->options(function (Forms\Get $get) {
                        // check if property_id is set
                        if ($get('property_id') != null && $get('property_id') != "") {
                            // get all tenants with a tenancy agreement for the property
                            return Tenant::query()
                                ->whereHas('tenancyAgreements', function (Builder $query) use ($get) {
                                    $query->where('status', '=', '1')
                                        ->where('unit_id', '=', $get('unit_id'))
                                        ->whereHas('property', function (Builder $query) use ($get) {
                                            $query->where('properties.id', '=', $get('property_id'));
                                        });
                                })
                                ->pluck('name', 'id');
                        }else{
                            return [];
                        }
                    })
                    ->label('Tenant')
                    ->reactive(),
                Forms\Components\DatePicker::make('notice_start_date')
                    ->label('Notice Start Date')
                    ->required(),
                Forms\Components\DatePicker::make('notice_end_date')
                    ->label('Notice End Date')
                    ->required(),
                Forms\Components\Textarea::make('extra_information')
                    ->label('Extra Information')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notice_start_date')
                    ->label('Notice Start Date')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notice_end_date')
                    ->label('Notice End Date')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(VacationNoticesExporter::class)
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
                    ->exporter(VacationNoticesExporter::class)
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
            'index' => Pages\ListVacationNotices::route('/'),
            'create' => Pages\CreateVacationNotice::route('/create'),
            'view' => Pages\ViewVacationNotice::route('/{record}'),
            'edit' => Pages\EditVacationNotice::route('/{record}/edit'),
        ];
    }
}
