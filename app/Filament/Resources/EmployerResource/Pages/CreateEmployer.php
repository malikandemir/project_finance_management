<?php

namespace App\Filament\Resources\EmployerResource\Pages;

use App\Filament\Resources\EmployerResource;
use App\Models\Account;
use App\Models\TheUniformChartOfAccount;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployer extends CreateRecord
{
    protected static string $resource = EmployerResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['account_type'] = '335'; // Set employer account type
        
        return $data;
    }
    
    protected function handleRecordCreation(array $data): Model
    {
        // Remove account_type from data before creating user
        $accountType = $data['account_type'] ?? '335';
        unset($data['account_type']);
        
        // Create the user
        $user = static::getModel()::create($data);
        
        // Create the account for the user with 335 account number
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
                'account_group_id' => $accountGroup->id, // Add the account_group_id
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
        return __('entities.employer_created_successfully');
    }
}
