<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Company;
use App\Models\TheUniformChartOfAccount;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainCompanyAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the main company
        $mainCompany = Company::where('is_main', true)->first();
        
        if (!$mainCompany) {
            $this->command->error('Main company not found. Please run UserRoleSeeder first.');
            return;
        }
        
        // Find the admin user (or any user to associate with the account)
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if (!$adminUser) {
            $this->command->error('Admin user not found. Please run UserRoleSeeder first.');
            return;
        }
        
        // Find the Default Account Group
        $defaultGroup = AccountGroup::where('name', 'Default Group')->first();
        
        if (!$defaultGroup) {
            $this->command->error('Default Account Group not found. Please run AccountGroupSeeder first.');
            return;
        }
        
        // Find the Uniform Chart of Account with number 100 (Cash)
        $cashUniformAccount = TheUniformChartOfAccount::where('number', '100')->first();
        
        if (!$cashUniformAccount) {
            $this->command->error('Cash account (100) not found in the Uniform Chart of Accounts.');
            return;
        }
        
        // Create Cash account for the main company if it doesn't exist
        Account::firstOrCreate(
            [
                'account_name' => 'Cash',
                'account_uniform_id' => $cashUniformAccount->id,
            ],
            [
                'balance' => 0.00,
                'account_group_id' => $defaultGroup->id,
                'user_id' => $adminUser->id,
            ]
        );
        
        $this->command->info('Cash account created for the main company successfully.');
    }
}
