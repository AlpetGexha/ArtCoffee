<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Actions\Notifications\SendOrderStatusNotification;
use App\Enum\OrderStatus;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Hook that is executed after the record is saved.
     */
    protected function afterSave(): void
    {
        // Check if status has been changed
        if ($this->record->wasChanged('status')) {
            // Get the notification action through dependency injection
            $notificationAction = app(SendOrderStatusNotification::class);

            // Get the previous status value as a string
            $previousStatusValue = $this->record->getOriginal('status')->value;

            // Send notification about the status change
            $notificationAction->handle(
                order: $this->record,
                previousStatus: $previousStatusValue ? OrderStatus::from($previousStatusValue) : null
            );
        }
    }
}
