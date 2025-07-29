<?php

namespace App\Observers;

use App\Helpers\MainCompanyHelper;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Company;
use App\Models\TheUniformChartOfAccount;

class CompanyObserver
{
    /**
     * Handle the Company "creating" event.
     */
    public function creating(Company $company): void
    {
        // If no owner_id is set, use the created_by user as owner
        if (!$company->owner_id && $company->created_by) {
            $company->owner_id = $company->created_by;
        }
        
        // Ensure company has an owner
        if (!$company->owner_id) {
            $company->owner_id = auth()->id();
        }
    }
    
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        // If this company is set as main, unset all other companies
        if ($company->is_main) {
            $this->unsetOtherMainCompanies($company);
        }
        
        // Create an account for the new company
        $this->createCompanyAccount($company);
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        // If this company is set as main, unset all other companies
        if ($company->is_main) {
            $this->unsetOtherMainCompanies($company);
        }
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        //
    }
    
    /**
     * Unset all other companies as main except the given company.
     */
    private function unsetOtherMainCompanies(Company $company): void
    {
        Company::where('id', '!=', $company->id)
            ->where('is_main', true)
            ->update(['is_main' => false]);
            
        // Clear the main company cache whenever main company changes
        MainCompanyHelper::clearCache();
    }
    
    /**
     * Create an account for the company.
     *
     * @param Company $company
     * @return void
     */
    private function createCompanyAccount(Company $company): void
    {
        // Find the uniform chart of account with number 120
        $uniformAccount = TheUniformChartOfAccount::where('number', '120')->first();
        
        if (!$uniformAccount) {
            // Log error if the uniform chart of account doesn't exist
            \Log::error('Could not create account for company: ' . $company->name . '. Uniform chart of account with number 120 not found.');
            return;
        }
        
        // Find the default account group
        $accountGroup = AccountGroup::where('name', 'Default Group')->first();
        
        if (!$accountGroup) {
            // Create the default account group if it doesn't exist
            $accountGroup = AccountGroup::create(['name' => 'Default Group']);
        }
        
        // Create a new account for the company
        Account::create([
            'account_name' => $company->name,
            'balance' => 0,
            'account_uniform_id' => $uniformAccount->id,
            'account_group_id' => $accountGroup->id,
        ]);
    }
}
