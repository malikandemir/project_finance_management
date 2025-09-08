<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Account;
use App\Models\TheUniformChartOfAccount;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['account_type'] = '120'; // Set customer account type
        
        return $data;
    }
    
    protected function handleRecordCreation(array $data): Model
    {
        // Remove account_type from data before creating user
        $accountType = $data['account_type'] ?? '120';
        unset($data['account_type']);
        
        // Create the user
        $user = static::getModel()::create($data);
        
        // Create the account for the user with 120 account number
        $uniformAccount = TheUniformChartOfAccount::where('number', $accountType)->first();
        
        if ($uniformAccount) {
            // Find or create the default account group
            $accountGroup = \App\Models\AccountGroup::where('name', 'Default Group')->first();
            if (!$accountGroup) {
                $accountGroup = \App\Models\AccountGroup::create(['name' => 'Default Group']);
            }
            
            Account::create([
                'account_name' => $user->name,
                'balance' => 0,
                'account_uniform_id' => $uniformAccount->id,
                'user_id' => $user->id,
                'account_group_id' => $accountGroup->id,
            ]);
        }
        
        return $user;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('entities.customer_created_successfully');
    }
}
