<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Paso 1: Cargar los datos del Usuario vinculado en el formulario
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Traemos el nombre y email del usuario relacionado para que aparezcan en los inputs
        $data['username'] = $this->record->user->name;
        $data['email'] = $this->record->user->email;

        return $data;
    }

    /**
     * Paso 2: Guardar los cambios en las dos tablas (users y clients)
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 1. Actualizar la tabla 'users'
        $record->user->update([
            'name'  => $data['username'],
            'email' => $data['email'],
            // La contraseña normalmente no se actualiza aquí por seguridad, 
            // a menos que decidas implementar una lógica de cambio de clave.
        ]);

        // 2. Actualizar la tabla 'clients' con todos tus campos de migración
        $record->update([
            'full_name'      => $data['full_name'],
            'dni_ruc'        => $data['dni_ruc'],
            'phone'          => $data['phone'],
            'address'        => $data['address'],
            'credit_limit'   => $data['credit_limit'],
            'status'         => $data['status'],
            'bank_name'      => $data['bank_name'],
            'account_number' => $data['account_number'],
        ]);

        return $record;
    }
}