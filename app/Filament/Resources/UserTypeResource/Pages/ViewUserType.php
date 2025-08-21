<?php

namespace App\Filament\Resources\UserTypeResource\Pages;

use App\Filament\Resources\UserTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationGroup;

class ViewUserType extends ViewRecord
{
    protected static string $resource = UserTypeResource::class;

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
                UserTypeResource\RelationManagers\AccountsRelationManager::class,
                UserTypeResource\RelationManagers\TransactionsRelationManager::class,
            ]),
        ];
    }
}
