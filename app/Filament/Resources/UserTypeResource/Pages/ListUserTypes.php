<?php

namespace App\Filament\Resources\UserTypeResource\Pages;

use App\Filament\Resources\UserTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUserTypes extends ListRecords
{
    protected static string $resource = UserTypeResource::class;
    
    protected function getHeaderActions(): array
    {
        // Get user type from request query parameter
        $userType = request()->query('user_type');
        
        // Default actions for all users
        $actions = [
            Actions\CreateAction::make()
                ->label(__('entities.add_user'))
                ->url(fn (): string => UserTypeResource::getUrl('create')),
        ];
        
        // Add specific user type actions
        $actions[] = Actions\Action::make('addCustomer')
            ->label(__('entities.add_customer'))
            ->url(fn (): string => UserTypeResource::getUrl('create', ['account_type' => '120', 'user_type' => 'customers']));
            
        $actions[] = Actions\Action::make('addSupplier')
            ->label(__('entities.add_supplier'))
            ->url(fn (): string => UserTypeResource::getUrl('create', ['account_type' => '320', 'user_type' => 'suppliers']));
            
        $actions[] = Actions\Action::make('addEmployer')
            ->label(__('entities.add_employer'))
            ->url(fn (): string => UserTypeResource::getUrl('create', ['account_type' => '335', 'user_type' => 'employers']));
        
        return $actions;
    }

}
