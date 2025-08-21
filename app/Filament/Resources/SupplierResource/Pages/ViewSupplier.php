<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Filament\Resources\UserTypeResource\RelationManagers;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationGroup;

class ViewSupplier extends ViewRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function getRelationManagers(): array
    {
        return [
            RelationGroup::make(__('entities.financial_information'), [
                RelationManagers\AccountsRelationManager::class,
                RelationManagers\TransactionsRelationManager::class,
            ]),
        ];
    }
}
