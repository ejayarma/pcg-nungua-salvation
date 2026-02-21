<?php

namespace App\Filament\Resources\MessageBroadcastResource\Pages;

use App\Filament\Resources\MessageBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMessageBroadcast extends EditRecord
{
    protected static string $resource = MessageBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
