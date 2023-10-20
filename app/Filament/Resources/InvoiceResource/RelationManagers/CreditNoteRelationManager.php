<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Models\CreditNote;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditNoteRelationManager extends RelationManager
{
    protected static string $relationship = 'creditNote';

    public function form(Form $form): Form
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
                Forms\Components\Toggle::make('is_confirmed')
                    ->label('Confirmed')
                    ->hiddenOn(['create']),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                // custom action to generate credit note documents
                Tables\Actions\Action::make('generate-credit-note-documents')
                    ->label('Generate Credit Note Documents')
                    ->icon('heroicon-o-document-text')
                    ->action(function(){
                        CreditNote::query()
                            ->where('is_confirmed', true)
                            ->where('is_document_generated', false)
                            ->chunk(1000, function ($creditNotes) {
                                foreach ($creditNotes as $creditNote) {
                                    $creditNote->generateCreditNoteDocument();
                                }
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->disabled(fn($record) => $record->is_document_generated),
//                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('View Document')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn($record) => !$record->is_document_generated)
                    ->url(function (CreditNote $creditNote) {
                        // preview the credit note
                        // TODO: Update the storage location of the credit notes for improved safety
                        return getenv('APP_URL').'/'.$creditNote->document_url;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make()
//                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
