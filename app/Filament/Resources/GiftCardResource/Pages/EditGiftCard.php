<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Filament\Resources\GiftCardResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

final class EditGiftCard extends EditRecord
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('mark_redeemed')
                ->label('Mark as Redeemed')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->hidden(fn () => $this->record->isRedeemed() || ! $this->record->isValid())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->redeemed_at = now();
                    $this->record->save();

                    Notification::make()
                        ->title('Gift Card Redeemed')
                        ->success()
                        ->body("Gift Card #{$this->record->id} has been marked as redeemed.")
                        ->send();

                    $this->refreshFormData(['redeemed_at']);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Gift Card Updated')
            ->success()
            ->body("Gift Card #{$this->record->id} has been updated successfully.")
            ->send();
    }
}
