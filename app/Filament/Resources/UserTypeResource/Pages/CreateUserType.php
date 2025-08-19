<?php

namespace App\Filament\Resources\UserTypeResource\Pages;

use App\Filament\Resources\UserTypeResource;
use App\Models\Account;
use App\Models\TheUniformChartOfAccount;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUserType extends CreateRecord
{
    protected static string $resource = UserTypeResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        $accountType = $this->data['account_type'] ?? request()->query('account_type');
        $userType = request()->query('user_type');
        
        if ($accountType === '120' || $userType === 'customers') {
            return __('Customer created successfully');
        } elseif ($accountType === '320' || $userType === 'suppliers') {
            return __('Supplier created successfully');
        } elseif ($accountType === '335' || $userType === 'employers') {
            return __('Employer created successfully');
        }
        
        return parent::getCreatedNotificationTitle();
    }
    
    protected function handleRecordCreation(array $data): Model
    {
        // Remove account_type from data before creating user
        $accountType = $data['account_type'] ?? request()->query('account_type');
        $userType = request()->query('user_type');
        unset($data['account_type']);
        
        // Create the user
        $user = static::getModel()::create($data);
        
        // Determine account type based on user type if not explicitly set
        if (!$accountType && $userType) {
            if ($userType === 'customers') {
                $accountType = '120';
            } elseif ($userType === 'suppliers') {
                $accountType = '320';
            } elseif ($userType === 'employers') {
                $accountType = '335';
            }
        }
        
        // Create the account for the user based on account type
        if ($accountType) {
            $uniformAccount = TheUniformChartOfAccount::where('number', $accountType)->first();
            
            if ($uniformAccount) {
                Account::create([
                    'account_name' => $user->name,
                    'balance' => 0,
                    'account_uniform_id' => $uniformAccount->id,
                    'user_id' => $user->id,
                ]);
            }
        }
        
        return $user;
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set account_type from query parameter if not already set
        if (!isset($data['account_type']) || empty($data['account_type'])) {
            $data['account_type'] = request()->query('account_type');
            
            // If still not set, try to determine from user type filter
            if (empty($data['account_type'])) {
                $userType = request()->query('user_type');
                
                if ($userType === 'customers') {
                    $data['account_type'] = '120';
                } elseif ($userType === 'suppliers') {
                    $data['account_type'] = '320';
                } elseif ($userType === 'employers') {
                    $data['account_type'] = '335';
                }
            }
        }
        
        return $data;
    }
}
