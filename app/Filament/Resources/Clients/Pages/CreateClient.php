<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use App\Models\Client;
use App\Models\Saving;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
       return DB::transaction(function () use ($data) {

        // 1. Usuario
        $user = User::create([
            'name'     => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('panel_user');

        // 2. Cliente (OBLIGATORIO antes de savings)
        $client = Client::create([
            'user_id'        => $user->id,
            'full_name'      => $data['full_name'],
            'dni_ruc'        => $data['dni_ruc'],
            'phone'          => $data['phone'] ?? null,
            'address'        => $data['address'] ?? null,
            'credit_limit'   => $data['credit_limit'] ?? 0,
            'status'         => 'active',
            'bank_name'      => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
        ]);

        // 3. Saving automático (UNO O MUCHOS)
        $client->savings()->create([
            'balance' => 0,
            'status'  => 'active',
        ]);

        return $client;
    });
    }
}