<?php

namespace App\Filament\Resources\Central;

use App\Models\Subscription;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\Central\SubscriptionResource\Pages;
use BackedEnum; 
use UnitEnum;   

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

   
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    
    protected static string|UnitEnum|null $navigationGroup = 'SaaS Management';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('saasClient.name')
                    ->label('Client')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}