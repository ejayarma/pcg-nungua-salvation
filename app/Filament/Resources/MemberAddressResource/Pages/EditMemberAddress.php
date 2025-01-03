<?php

namespace App\Filament\Resources\MemberAddressResource\Pages;

use App\Filament\Resources\MemberAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberAddress extends EditRecord
{
    protected static string $resource = MemberAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
