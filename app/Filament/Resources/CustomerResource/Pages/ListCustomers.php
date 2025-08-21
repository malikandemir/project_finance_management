<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('entities.add_customer'))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['account_type'] = '120'; // Set customer account type
                    return $data;
                }),
        ];
    }
}
