<?php

namespace App\Filament\Resources\GenerationalGroupResource\Pages;

use App\Filament\Resources\GenerationalGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGenerationalGroups extends ListRecords
{
    protected static string $resource = GenerationalGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
