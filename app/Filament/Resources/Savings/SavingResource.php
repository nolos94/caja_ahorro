<?php

namespace App\Filament\Resources\Savings;

use App\Filament\Resources\Savings\Pages\CreateSaving;
use App\Filament\Resources\Savings\Pages\EditSaving;
use App\Filament\Resources\Savings\Pages\ListSavings;
use App\Filament\Resources\Savings\Pages\ViewSaving;
use App\Filament\Resources\Savings\Schemas\SavingForm;
use App\Filament\Resources\Savings\Schemas\SavingInfolist;
use App\Filament\Resources\Savings\Tables\SavingsTable;
use App\Models\Saving;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Savings\RelationManagers\InstallmentsRelationManager;
use App\Filament\Resources\Savings\RelationManagers\TransactionsRelationManager;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return SavingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SavingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SavingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //

            TransactionsRelationManager::class,
            InstallmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSavings::route('/'),
            'create' => CreateSaving::route('/create'),
            'view' => ViewSaving::route('/{record}'),
            'edit' => EditSaving::route('/{record}/edit'),
        ];
    }
}
