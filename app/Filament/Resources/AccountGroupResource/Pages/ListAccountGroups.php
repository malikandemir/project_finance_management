<?php

namespace App\Filament\Resources\AccountGroupResource\Pages;

use App\Filament\Resources\AccountGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccountGroups extends ListRecords
{
    protected static string $resource = AccountGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
