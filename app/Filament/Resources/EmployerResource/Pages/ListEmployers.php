<?php

namespace App\Filament\Resources\EmployerResource\Pages;

use App\Filament\Resources\EmployerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployers extends ListRecords
{
    protected static string $resource = EmployerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('entities.add_employer'))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['account_type'] = '335'; // Set employer account type
                    return $data;
                }),
        ];
    }
}
