<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\VacationNoticesExporter;
use App\Filament\Resources\App\VacationNoticeResource\Pages;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\VacationNotices;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VacationNoticeResource extends Resource
{
    protected static ?string $model = VacationNotices::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::TENANCY_MANAGEMENT_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-left-circle';

    /*
    |--------------------------------------------------------------------------
    | TENANT SCOPE — Phase 10.4
    |--------------------------------------------------------------------------
    */
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
                ->options(Property::where('saas_client_id', $tenantId)->pluck('name', 'id'))
                ->label('Property')
                ->required()
                ->reactive(),
            Forms\Components\Select::make('unit_id')
                ->required()
                ->options(function (Forms\Get $get) use ($tenantId) {
                    if ($get('property_id')) {
                        return \App\Models\Unit::query()
                            ->where('saas_client_id', $tenantId)
                            ->where('property_id', $get('property_id'))
                            ->pluck('name', 'id');
                    }
                    return [];
                })
                ->label('Unit')
                ->reactive(),
            Forms\Components\Select::make('tenant_id')
                ->options(function (Forms\Get $get) use ($tenantId) {
                    if ($get('property_id')) {
                        return Tenant::query()
                            ->where('saas_client_id', $tenantId)
                            ->whereHas('tenancyAgreements', function (Builder $query) use ($get) {
                                $query->where('status', '=', '1')
                                    ->where('unit_id', '=', $get('unit_id'))
                                    ->whereHas('property', function (Builder $q) use ($get) {
                                        $q->where('properties.id', '=', $get('property_id'));
                                    });
                            })
                            ->pluck('name', 'id');
                    }
                    return [];
                })
                ->label('Tenant')
                ->reactive(),
            Forms\Components\DatePicker::make('notice_start_date')->label('Notice Start Date')->required(),
            Forms\Components\DatePicker::make('notice_end_date')->label('Notice End Date')->required(),
            Forms\Components\Textarea::make('extra_information')->label('Extra Information')->columnSpanFull()->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.name')->label('Property')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.name')->label('Unit')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')->label('Tenant')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('notice_start_date')->label('Notice Start Date')->date('F jS, Y')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('notice_end_date')->label('Notice End Date')->date('F jS, Y')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(VacationNoticesExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->requiresConfirmation(),
                ]),
                ExportBulkAction::make()->exporter(VacationNoticesExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVacationNotices::route('/'),
            'create' => Pages\CreateVacationNotice::route('/create'),
            'view'   => Pages\ViewVacationNotice::route('/{record}'),
            'edit'   => Pages\EditVacationNotice::route('/{record}/edit'),
        ];
    }
}
