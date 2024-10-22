<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyDetailsResource\Pages;
use App\Filament\Resources\CompanyDetailsResource\RelationManagers;
use App\Models\CompanyDetails;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Utils\AppUtils;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CompanyDetailsResource extends Resource
{
    protected static ?string $model = CompanyDetails::class;

    protected static ?string $navigationGroup = AppUtils::ACCESS_MANAGEMENT_NAVIGATION_GROUP;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Company name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->label('Physical address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('account_number')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('bank_name')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('bank_branch')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('branch_swift_code')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('mpesa_paybill_number')
                    ->numeric()
                    ->required()
                    ->maxLength(20),
                Forms\Components\FileUpload::make('logo')
                    ->label('Company logo')
                    ->directory('logos')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_branch')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch_swift_code')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('mpesa_paybill_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('archive')
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->numeric()
                    ->sortable()
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
                Tables\Actions\Action::make('View logo')
                    ->url(function (CompanyDetails $company){
                        $fileName = str_replace('logos/', '', $company->logo);
                        return route('preview.company-logo', ['companyLogo' => $fileName]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation("Are you sure you want to delete this company?"),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
