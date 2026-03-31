<?php

namespace App\Filament\Resources\App;

use App\Filament\Exports\ClientExporter;
use App\Filament\Resources\App\ClientResource\Pages;
use App\Models\Client;
use App\Models\CompanyDetails;
use App\Utils\AppPermissions;
use App\Utils\AppUtils;
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

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|\UnitEnum|null $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

   
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('saas_client_id', filament()->getTenant()?->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->label('Name')->required()->maxLength(255),
            Forms\Components\TextInput::make('email')->label('Email')->nullable()->email()->maxLength(255),
            Forms\Components\TextInput::make('phone_number')->label('Phone Number')->nullable()->required()->maxLength(255),
            Forms\Components\TextInput::make('address')->label('Address')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->label('Description')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('status')->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('pdf')
                    ->label('Generate PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->visible(fn () => auth()->user()->can(AppPermissions::GENERATE_CLIENT_PDF))
                    ->action(function ($record) {
                        $client  = $record->load('manualInvoices');
                        $company = CompanyDetails::latest()->first();
                        if (!$company) {
                            throw new \Exception('Company details not found. Please set up company details first.');
                        }
                        $pdf = Pdf::loadView('pdfs.client-details', [
                            'client'    => $client,
                            'company'   => $company,
                            'timestamp' => now()->format('Y-m-d H:i:s'),
                        ]);
                        $pdf->setPaper('A4', 'portrait');
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "{$client->name}-{$client->id}-details.pdf"
                        );
                    }),
            ])
            ->headerActions([
                ExportAction::make()->exporter(ClientExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()->requiresConfirmation()]),
                ExportBulkAction::make()->exporter(ClientExporter::class)->formats([ExportFormat::Csv])->fileDisk('local'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit'   => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
