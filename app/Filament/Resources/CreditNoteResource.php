<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditNoteResource\Pages;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Utils\AppUtils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditNoteResource extends Resource
{
    protected static ?string $model = CreditNote::class;

    protected static ?string $navigationGroup = AppUtils::ACCOUNTING_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount_credited')
                    ->label('Amount Credited')
                    ->required()
                    ->minValue(1)
                    ->numeric()
                    ->step(0.01),
                Forms\Components\Textarea::make('reason_for_issuance')
                    ->label('Reason for Issuance')
                    ->required()
                    ->rows(4)
                    ->maxLength(1000),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(4)
                    ->maxLength(1000),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount_credited')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date('Fs j, Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason_for_issuance')
                    // show only 20 characters
                    ->limit(20)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    // show only 20 characters
                    ->limit(20)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_confirmed')
                    ->label('Confirmed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_document_generated')
                    ->label('Doc Generated')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('Fs j, Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date('Fs j, Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->label('Updated By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('View Document')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn($record) => !$record->is_document_generated)
                    ->url(function (CreditNote $creditNote) {
                        if (!$creditNote->is_document_generated) {
                            return route('preview.credit-note', ['creditNote' => null]);
                        }

                        $fileName = str_replace('credit-notes/', '', $creditNote->document_url);
                        return route('preview.credit-note', ['creditNote' => $fileName]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make()
//                        ->requiresConfirmation(),
                ]),
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
            'index' => Pages\ListCreditNotes::route('/'),
//            'create' => Pages\CreateCreditNote::route('/create'),
            'view' => Pages\ViewCreditNote::route('/{record}'),
//            'edit' => Pages\EditCreditNote::route('/{record}/edit'),
        ];
    }
}
