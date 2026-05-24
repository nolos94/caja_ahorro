<?php

namespace App\Filament\Resources\Loans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use App\Enums\LoanStatus;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.full_name')->searchable(),
                TextColumn::make('amount')->numeric()->sortable(),
                TextColumn::make('interest_rate')->numeric()->sortable(),
                TextColumn::make('installments_count')->numeric()->sortable(),
                TextColumn::make('frequency')->badge(),
                TextColumn::make('total_interest')->numeric()->sortable(),
                TextColumn::make('total_amount')->numeric()->sortable(),
                TextColumn::make('balance')->numeric()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('approved_at')->dateTime()->sortable(),
                TextColumn::make('approver.name')->numeric()->sortable(),
                TextColumn::make('disbursement_date')->date()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->actions([
                ActionGroup::make([
                    // 1. Enviar a revisión
                    Action::make('send_to_approval')
                        ->label('Enviar a Aprobación')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status === LoanStatus::DRAFT)
                        ->action(fn ($record) => $record->update(['status' => LoanStatus::PENDING_APPROVAL])),

                    // 2. Aprobar
                    Action::make('approve')
                        ->label('Aprobar Préstamo')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status === LoanStatus::PENDING_APPROVAL)
                        ->action(function ($record) {
                            $record->update([
                                'status' => LoanStatus::APPROVED,
                                'approved_at' => now(),
                                'approved_by' => auth()->id(),
                                'notes' => "El préstamo se encuentra aprobado, pendiente de realizar el desembolso.",
                            ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Préstamo Aprobado')
                                    ->body('El préstamo ha sido aprobado, el desembolso esta pendiente.')
                                    ->success()
                                    ->send();
                        }),

                    // 3. Rechazar (Usamos la ruta completa del Textarea para no romper tus use)
                    Action::make('reject')
                        ->label('Rechazar Préstamo')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            \Filament\Forms\Components\Textarea::make('rejection_reason')
                                ->label('Motivo del rechazo')
                                ->required(),
                        ])
                        ->visible(fn ($record) => $record->status === LoanStatus::PENDING_APPROVAL)
                        ->action(fn ($record, array $data) => $record->update([
                            'status' => LoanStatus::REJECTED,
                            'notes' => "Rechazo: " . $data['rejection_reason'],
                        ])),

                            // Esta es tu acción de "Activar" (Desembolsar)
                    Action::make('activate')
                        ->label('Activar (Desembolsar)')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('¿Confirmar Desembolso?')
                        ->modalDescription('Al activar, se registrará la fecha de hoy como inicio del préstamo y se generará el calendario de pagos.')
                        ->visible(fn ($record) => $record->status === LoanStatus::APPROVED)
                        ->action(function ($record) {

                            $record->update([
                                'status' => LoanStatus::ACTIVE,
                                'disbursement_date' => now(),
                                'notes' => "El préstamo se encuentra desembolsado correctamente.",
                            ]);

                            $service = app(\App\Services\LoanService::class);
                            $service->persistInstallments($record);

                            \Filament\Notifications\Notification::make()
                                ->title('Préstamo Activado')
                                ->body('El dinero ha sido desembolsado y las cuotas han sido generadas.')
                                ->success()
                                ->send();
                        }),
                    ]),
                ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}