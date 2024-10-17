<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ManualInvoicesExporter;
use App\Filament\Resources\ManualInvoicesResource\Pages;
use App\Filament\Resources\ManualInvoicesResource\RelationManagers;
use App\Models\Client;
use App\Models\ManualInvoices;
use App\Models\PropertyOwners;
use App\Models\Tenant;
use Filament\Actions\ViewAction;
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

class ManualInvoicesResource extends Resource
{
    protected static ?string $model = ManualInvoices::class;

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
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ManualInvoicesExporter::class)
                    ->formats([
                        ExportFormat::Csv
                    ])
                
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
