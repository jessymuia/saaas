<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VacationNoticesRelationManager extends RelationManager
{
    protected static string $relationship = 'vacationNotices';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.unit.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenancyAgreement.tenant.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notice_start_date')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notice_end_date')
                    ->date('F jS, Y')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
