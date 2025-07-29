<?php

namespace App\Filament\Resources\AccountGroupResource\Pages;

use App\Filament\Resources\AccountGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountGroup extends EditRecord
{
    protected static string $resource = AccountGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
