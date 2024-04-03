<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Utils\AppUtils;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $recordTitleAttribute = "Invoice";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('comments')
                    ->maxLength(1000),
                Forms\Components\DatePicker::make('issue_date')
                    ->disabled()
                    ->readOnly(),
                Forms\Components\DatePicker::make('created_at')
                    ->disabled()
                    ->readOnly()
                    ->required(),
                Forms\Components\Toggle::make('is_confirmed')
                    ->label('Confirmed')
                    ->required(),
                Forms\Components\Toggle::make('is_generated')
                    ->label('Doc Generated')
                    ->disabled()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.property.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unpaid_amount')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->boolean()
                    ->sortable()
                    ->label('Confirmed'),
                Tables\Columns\IconColumn::make('is_generated')
                    ->boolean()
                    ->label('Doc Generated'),
                Tables\Columns\TextColumn::make('comments')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_for_month')
                    ->date('F, Y')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('View Invoice')
                ->icon('heroicon-o-document-text')
                ->disabled(fn (Invoice $invoice) => !$invoice->is_generated)
                ->url(function (Invoice $invoice) {
                    if (!$invoice->is_generated) {
                        return route('preview.invoice',['invoice'=>null]);
                    }
                    $fileName = str_replace('invoices/','',$invoice->document_url);
                    return route('preview.invoice',['invoice'=>$fileName]);
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\TenancyBillsRelationManager::class,
            RelationManagers\CreditNoteRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
