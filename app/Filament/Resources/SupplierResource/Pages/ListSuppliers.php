<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('entities.add_supplier'))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['account_type'] = '320'; // Set supplier account type
                    return $data;
                }),
        ];
    }
}
