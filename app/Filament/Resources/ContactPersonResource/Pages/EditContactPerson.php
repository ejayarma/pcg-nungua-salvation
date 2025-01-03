<?php

namespace App\Filament\Resources\ContactPersonResource\Pages;

use App\Filament\Resources\ContactPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactPerson extends EditRecord
{
    protected static string $resource = ContactPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
