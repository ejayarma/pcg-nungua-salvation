<?php

namespace App\Filament\Resources\ContactPersonResource\Pages;

use App\Filament\Resources\ContactPersonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactPerson extends CreateRecord
{
    protected static string $resource = ContactPersonResource::class;
}
