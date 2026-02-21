<?php

namespace App\Filament\Resources\MessageBroadcastResource\Pages;

use App\Filament\Resources\MessageBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMessageBroadcasts extends ListRecords
{
    protected static string $resource = MessageBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
