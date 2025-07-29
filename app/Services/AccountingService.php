<?php

namespace App\Services;

use App\Helpers\MainCompanyHelper;
use App\Models\Account;
use App\Models\Company;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Get the main company for accounting operations.
     *
     * @return Company|null
     */
    public function getMainCompany()
    {
        return MainCompanyHelper::getMainCompany();
    }
    
    /**
     * Get all accounts for the main company.
     *
     * @return Collection
     */
    public function getMainCompanyAccounts()
    {
        $mainCompany = $this->getMainCompany();
        
        if (!$mainCompany) {
            return new Collection();
        }
        
        // Here you would implement the logic to get accounts related to the main company
        // This is a placeholder implementation - you'll need to adjust based on your actual data structure
        return Account::all(); // Replace with actual relationship logic
    }
    
    /**
     * Get the account balance for a specific account in the main company.
     *
     * @param int $accountId
     * @return float
     */
    public function getAccountBalance($accountId)
    {
        $mainCompany = $this->getMainCompany();
        
        if (!$mainCompany) {
            return 0;
        }
        
        $account = Account::find($accountId);
        
        if (!$account) {
            return 0;
        }
        
        return $account->balance;
    }
    
    /**
     * Record a transaction for the main company.
     *
     * @param int $debitAccountId
     * @param int $creditAccountId
     * @param float $amount
     * @param string $description
     * @param string|null $reference
     * @return Transaction
     */
    public function recordTransaction($debitAccountId, $creditAccountId, $amount, $description, $reference = null)
    {
        $mainCompany = $this->getMainCompany();
        
        if (!$mainCompany) {
            throw new \Exception('No main company defined for accounting operations.');
        }
        
        // Start a database transaction to ensure both debit and credit are recorded
        return DB::transaction(function () use ($debitAccountId, $creditAccountId, $amount, $description, $reference, $mainCompany) {
            // Create the transaction record
            $transaction = new Transaction([
                'amount' => $amount,
                'description' => $description,
                'reference' => $reference,
                'transaction_date' => now(),
                // Add any other fields needed for your transaction
            ]);
            
            // Save the transaction
            $transaction->save();
            
            // Update account balances
            $debitAccount = Account::findOrFail($debitAccountId);
            $creditAccount = Account::findOrFail($creditAccountId);
            
            // Increase debit account (assets, expenses)
            $debitAccount->balance += $amount;
            $debitAccount->save();
            
            // Increase credit account (liabilities, equity, revenue)
            $creditAccount->balance += $amount;
            $creditAccount->save();
            
            return $transaction;
        });
    }
    
    /**
     * Get the balance sheet for the main company.
     *
     * @return array
     */
    public function getBalanceSheet()
    {
        $mainCompany = $this->getMainCompany();
        
        if (!$mainCompany) {
            return [
                'assets' => [],
                'liabilities' => [],
                'equity' => [],
            ];
        }
        
        // Here you would implement the logic to generate a balance sheet
        // This is a placeholder implementation
        return [
            'assets' => $this->getAssetAccounts(),
            'liabilities' => $this->getLiabilityAccounts(),
            'equity' => $this->getEquityAccounts(),
        ];
    }
    
    /**
     * Get asset accounts for the main company.
     *
     * @return Collection
     */
    private function getAssetAccounts()
    {
        // This is a placeholder implementation
        // You would implement logic to get asset accounts based on your chart of accounts
        return Account::whereHas('uniformChartOfAccount', function ($query) {
            $query->where('account_type', 'asset');
        })->get();
    }
    
    /**
     * Get liability accounts for the main company.
     *
     * @return Collection
     */
    private function getLiabilityAccounts()
    {
        // This is a placeholder implementation
        return Account::whereHas('uniformChartOfAccount', function ($query) {
            $query->where('account_type', 'liability');
        })->get();
    }
    
    /**
     * Get equity accounts for the main company.
     *
     * @return Collection
     */
    private function getEquityAccounts()
    {
        // This is a placeholder implementation
        return Account::whereHas('uniformChartOfAccount', function ($query) {
            $query->where('account_type', 'equity');
        })->get();
    }
}
