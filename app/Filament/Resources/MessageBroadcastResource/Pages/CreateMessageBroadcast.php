<?php

namespace App\Filament\Resources\MessageBroadcastResource\Pages;

use App\Filament\Resources\MessageBroadcastResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMessageBroadcast extends CreateRecord
{
    protected static string $resource = MessageBroadcastResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }
}
