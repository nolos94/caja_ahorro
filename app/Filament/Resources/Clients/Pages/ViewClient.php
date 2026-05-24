<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\EditAction;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Livewire\ClientBalanceHero::class,
            \App\Livewire\ClientLoansBalanceHero::class,
        ];
    }
     public function getHeaderWidgetsColumns(): int|array
    {
        return [
            2
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}