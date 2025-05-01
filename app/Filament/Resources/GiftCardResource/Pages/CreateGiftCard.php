<?php

namespace App\Filament\Resources\GiftCardResource\Pages;

use App\Filament\Resources\GiftCardResource;
use App\Models\GiftCard;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

final class CreateGiftCard extends CreateRecord
{
    protected static string $resource = GiftCardResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate activation key if not provided
        if (! isset($data['activation_key']) || empty($data['activation_key'])) {
            $data['activation_key'] = GiftCard::generateUniqueActivationKey();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Send notification about successful creation
        Notification::make()
            ->title('Gift Card Created')
            ->success()
            ->body("Gift Card has been created successfully with amount \${$this->record->amount}.")
            ->send();
    }
}
