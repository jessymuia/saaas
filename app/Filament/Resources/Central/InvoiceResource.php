<?php

namespace App\Filament\Resources\Central;

use App\Models\Invoice; // Ensure this points to your Central Invoice model
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use App\Filament\Resources\Central\InvoiceResource\Pages;
use BackedEnum;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    
    protected static string|UnitEnum|null $navigationGroup = 'SaaS Management';

    
    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saasClient.name')
                    ->label('Client')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}