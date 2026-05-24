<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Forms;
use Filament\Schemas\Schema; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section; 
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Settings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use HasPageShield;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.settings';
    protected static ?string $navigationLabel = 'Configuración';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            Setting::first()?->toArray() ?? []
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data') 
            ->components([
                Section::make('Empresa')
                    ->schema([
                        TextInput::make('company_name')->required(),
                        FileUpload::make('logo')
                            ->image()
                            ->directory('logos'),
                        TextInput::make('email')->email(),
                        TextInput::make('phone'),
                        Textarea::make('address'),
                    ]),

                Section::make('Finanzas')
                    ->schema([
                        Select::make('currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                            ])
                            ->required(),
                        TextInput::make('interest_rate')
                            ->numeric()
                            ->suffix('%'),
                        TextInput::make('late_fee')
                            ->numeric()
                            ->label('Recargo mora'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $setting = Setting::first() ?? new Setting();
        $setting->fill($state);
        $setting->save();

        cache()->forget('settings');

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}