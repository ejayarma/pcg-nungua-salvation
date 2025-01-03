<?php

namespace App\Filament\Resources\MemberAddressResource\Pages;

use App\Filament\Resources\MemberAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberAddresses extends ListRecords
{
    protected static string $resource = MemberAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
