<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Pages\ViewClient; // IMPORTANTE: Añadir este import
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Schemas\ClientInfolist; // Para la vista de detalle
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Clients\RelationManagers\LoansRelationManager;
use App\Filament\Resources\Clients\RelationManagers\SavingsRelationManager;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'full_name'; // Cambiado a un atributo real del modelo

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    // AÑADIR este método para que la vista funcione con tu esquema
    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            LoansRelationManager::class,
            SavingsRelationManager::class,
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'), // Quitamos el "Pages\" porque ya lo importamos arriba
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}