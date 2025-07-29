<?php

namespace App\Filament\Resources\TransactionGroupResource\Pages;

use App\Filament\Resources\TransactionGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionGroup extends EditRecord
{
    protected static string $resource = TransactionGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
