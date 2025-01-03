<?php

namespace App\Filament\Resources\ContactPersonResource\Pages;

use App\Filament\Resources\ContactPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactPeople extends ListRecords
{
    protected static string $resource = ContactPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
