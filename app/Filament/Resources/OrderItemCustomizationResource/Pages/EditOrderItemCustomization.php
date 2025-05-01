<?php

namespace App\Filament\Resources\OrderItemCustomizationResource\Pages;

use App\Filament\Resources\OrderItemCustomizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

final class EditOrderItemCustomization extends EditRecord
{
    protected static string $resource = OrderItemCustomizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
