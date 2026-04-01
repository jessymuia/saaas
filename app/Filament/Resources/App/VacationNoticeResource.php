<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\VacationNoticesExporter;
use App\Filament\Resources\App\VacationNoticeResource\Pages;
use App\Models\Property;
use App\Models\TenancyAgreement;
use App\Models\VacationNotices;
use App\Utils\AppUtils;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
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
                ->label('Property')
                ->required()
                ->reactive()
                ->options(Property::where('saas_client_id', $tenantId)->pluck('name', 'id'))
                ->afterStateUpdated(fn (Forms\Set $set) => $set('tenancy_agreement_id', null)),

            Forms\Components\Select::make('tenancy_agreement_id')
                ->label('Tenant / Unit')
                ->required()
                ->reactive()
                ->options(function (Forms\Get $get) use ($tenantId) {
                    if (!$get('property_id')) {
                        return [];
                    }
                    return TenancyAgreement::query()
                        ->where('saas_client_id', $tenantId)
                        ->where('status', true)
                        ->whereHas('unit', fn (Builder $q) => $q->where('property_id', $get('property_id')))
                        ->with(['tenant', 'unit'])
                        ->get()
                        ->mapWithKeys(fn ($agreement) => [
                            $agreement->id => "{$agreement->tenant->name} — {$agreement->unit->name}",
                        ]);
                })
                ->helperText('Select property first, then choose the active tenancy agreement'),

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
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(VacationNoticesExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->requiresConfirmation(),
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
