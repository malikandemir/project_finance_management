<?php

namespace App\Observers;

use App\Helpers\MainCompanyHelper;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Task;
use App\Models\TheUniformChartOfAccount;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        // Only process if task has a price
        if (!$task->price || $task->price <= 0) {
            return;
        }

        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create a transaction group for all related transactions
            $transactionGroup = TransactionGroup::create([
                'name' => "Task #{$task->id} - {$task->name}",
                'description' => "Transactions for Task #{$task->id} in Project #{$task->project_id}",
                'group_date' => now(),
                'user_id' => $task->project->responsible_user_id,
            ]);
            
            // Get the main company
            $mainCompany = MainCompanyHelper::getMainCompany();
            if (!$mainCompany) {
                throw new \Exception('No main company found for accounting operations');
            }
            
            // Get the project company
            $projectCompany = $task->project->company;
            if (!$projectCompany) {
                throw new \Exception('No company found for the task project');
            }
            
            // Get or create accounts
            $mainCompanyAccount = $this->getOrCreateAccount($mainCompany->name, '600', $mainCompany->owner_id);
            $projectCompanyAccount = $this->getOrCreateAccount($projectCompany->name, '120', $projectCompany->owner_id);
            
            // Amount to use for transactions
            $amount = $task->price;
            
            // Create credit transaction for main company (600) - Revenue increases with credit
            $newMainBalance = $mainCompanyAccount->balance - $amount;
            $description = "Project #{$task->project_id}, Task #{$task->id}, {$mainCompany->name} (Revenue)";
            $this->createTransaction(
                $amount,
                Transaction::CREDIT,
                $mainCompanyAccount->id,
                $task->project->responsible_user_id,
                $newMainBalance,
                substr($description, 0, 120),
                $transactionGroup->id
            );
            
            // Update main company account balance
            $mainCompanyAccount->balance = $newMainBalance;
            $mainCompanyAccount->save();
            
            // Create debit transaction for project company (120) - Receivable increases with debit
            $newProjectBalance = $projectCompanyAccount->balance + $amount;
            $description = "Project #{$task->project_id}, Task #{$task->id}, {$projectCompany->name} (Receivable)";
            $this->createTransaction(
                $amount,
                Transaction::DEBIT,
                $projectCompanyAccount->id,
                $task->project->responsible_user_id,
                $newProjectBalance,
                substr($description, 0, 120),
                $transactionGroup->id
            );
            
            // Update project company account balance
            $projectCompanyAccount->balance = $newProjectBalance;
            $projectCompanyAccount->save();
            
            // Additional transactions for responsible person
            // Get the responsible user
            $responsibleUser = $task->assignedUser;
            if (!$responsibleUser) {
                throw new \Exception('No responsible user found for the task project');
            }
            
            // Calculate amount based on cost_percentage or revenue_percentage
            $percentage = $task->cost_percentage > 0 ? $task->cost_percentage : $responsibleUser->revenue_percentage;
            $commissionAmount = $task->price * ($percentage / 100);
            
            if ($commissionAmount > 0) {
                // Get or create accounts
                $mainCompanyExpenseAccount = $this->getOrCreateAccount($mainCompany->name . ' - Expenses', '621', $mainCompany->owner_id);
                $responsiblePersonAccount = $this->getOrCreateAccount($responsibleUser->name, '320', $responsibleUser->id);
                
                // Create debit transaction for main company expense account (621) - Expense increases with debit
                $newExpenseBalance = $mainCompanyExpenseAccount->balance + $commissionAmount;
                $description = "Project #{$task->project_id}, Task #{$task->id}, {$mainCompany->name} (Expense)";
                $this->createTransaction(
                    $commissionAmount,
                    Transaction::DEBIT,
                    $mainCompanyExpenseAccount->id,
                    $responsibleUser->id,
                    $newExpenseBalance,
                    substr($description, 0, 120),
                    $transactionGroup->id
                );
                
                // Update main company expense account balance
                $mainCompanyExpenseAccount->balance = $newExpenseBalance;
                $mainCompanyExpenseAccount->save();
                
                // Create credit transaction for responsible person account (320) - Liability increases with credit
                $newPersonBalance = $responsiblePersonAccount->balance - $commissionAmount;
                $description = "Project #{$task->project_id}, Task #{$task->id}, {$responsibleUser->name} (Commission)";
                $this->createTransaction(
                    $commissionAmount,
                    Transaction::CREDIT,
                    $responsiblePersonAccount->id,
                    $responsibleUser->id,
                    $newPersonBalance,
                    substr($description, 0, 120),
                    $transactionGroup->id
                );
                
                // Update responsible person account balance
                $responsiblePersonAccount->balance = $newPersonBalance;
                $responsiblePersonAccount->save();
            }
            
            // Commit transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error processing task accounting: ' . $e->getMessage());
        }
    }

    /**
     * Get or create an account for the given name and uniform chart number.
     *
     * @param string $accountName
     * @param string $uniformNumber
     * @return Account
     * @throws \Exception
     */
    private function getOrCreateAccount(string $accountName, string $uniformNumber, int $userId): Account
    {
        // Find the uniform chart of account
        $uniformAccount = TheUniformChartOfAccount::where('number', $uniformNumber)->first();
        if (!$uniformAccount) {
            throw new \Exception("Uniform chart of account with number {$uniformNumber} not found");
        }
        
        // Find or create the default account group
        $accountGroup = AccountGroup::where('name', 'Default Group')->first();
        if (!$accountGroup) {
            $accountGroup = AccountGroup::create(['name' => 'Default Group']);
        }
        
        // Find or create the account
        $account = Account::where('account_name', $accountName)
            ->where('account_uniform_id', $uniformAccount->id)
            ->first();
            
        if (!$account) {
            $account = Account::create([
                'account_name' => $accountName,
                'balance' => 0,
                'account_uniform_id' => $uniformAccount->id,
                'account_group_id' => $accountGroup->id,
                'user_id' => $userId,
            ]);
        }
        
        return $account;
    }
    
    /**
     * Create a transaction.
     *
     * @param float $amount
     * @param int $debitCredit
     * @param int $accountId
     * @param int|null $userId
     * @param float $balanceAfterTransaction
     * @return Transaction
     */
    private function createTransaction(
        float $amount,
        int $debitCredit,
        int $accountId,
        ?int $userId,
        float $balanceAfterTransaction,
        ?string $description = null,
        ?int $transactionGroupId = null
    ): Transaction {
        return Transaction::create([
            'amount' => $amount,
            'debit_credit' => $debitCredit,
            'account_id' => $accountId,
            'user_id' => $userId,
            'transaction_group_id' => $transactionGroupId,
            'balance_after_transaction' => $balanceAfterTransaction,
            'transaction_date' => now(),
            'description' => $description,
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Check if price was changed
        if (!$task->isDirty('price') || !$task->price) {
            return;
        }

        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Find the transaction group for this task
            $transactionGroup = TransactionGroup::where('name', "Task #{$task->id} - {$task->name}")->first();
            
            if (!$transactionGroup) {
                // If no transaction group exists, treat it as a new task
                $this->created($task);
                return;
            }
            
            // Get all transactions in this group
            $transactions = Transaction::where('transaction_group_id', $transactionGroup->id)->get();
            
            if ($transactions->isEmpty()) {
                // If no transactions exist, treat it as a new task
                $this->created($task);
                return;
            }
            
            // Get the main company
            $mainCompany = MainCompanyHelper::getMainCompany();
            if (!$mainCompany) {
                throw new \Exception('No main company found for accounting operations');
            }
            
            // Get the project company
            $projectCompany = $task->project->company;
            if (!$projectCompany) {
                throw new \Exception('No company found for the task project');
            }
            
            // Get the responsible user
            $responsibleUser = $task->project->responsibleUser;
            if (!$responsibleUser) {
                throw new \Exception('No responsible user found for the task project');
            }
            
            // Get accounts
            $mainCompanyAccount = Account::where('account_name', $mainCompany->name)
                ->where('user_id', $mainCompany->owner_id)
                ->first();
                
            $projectCompanyAccount = Account::where('account_name', $projectCompany->name)
                ->where('user_id', $projectCompany->owner_id)
                ->first();
                
            $mainCompanyExpenseAccount = Account::where('account_name', $mainCompany->name . ' - Expenses')
                ->where('user_id', $mainCompany->owner_id)
                ->first();
                
            $responsiblePersonAccount = Account::where('account_name', $responsibleUser->name)
                ->where('user_id', $responsibleUser->id)
                ->first();
            
            // New amount
            $newAmount = $task->price;
            
            // Calculate new commission amount
            $percentage = $task->cost_percentage > 0 ? $task->cost_percentage : $responsibleUser->revenue_percentage;
            $newCommissionAmount = $newAmount * ($percentage / 100);
            
            // Update each transaction
            foreach ($transactions as $transaction) {
                // Determine which transaction this is based on account_id and debit_credit
                if ($transaction->account_id === $mainCompanyAccount->id && $transaction->debit_credit === Transaction::CREDIT) {
                    // This is the main company revenue transaction
                    $oldAmount = $transaction->amount;
                    $diffAmount = $newAmount - $oldAmount;
                    
                    // Update account balance (credit decreases balance)
                    $mainCompanyAccount->balance -= $diffAmount;
                    $mainCompanyAccount->save();
                    
                    // Update transaction
                    $transaction->amount = $newAmount;
                    $transaction->balance_after_transaction = $mainCompanyAccount->balance;
                    $transaction->save();
                    
                } else if ($transaction->account_id === $projectCompanyAccount->id && $transaction->debit_credit === Transaction::DEBIT) {
                    // This is the project company receivable transaction
                    $oldAmount = $transaction->amount;
                    $diffAmount = $newAmount - $oldAmount;
                    
                    // Update account balance (debit increases balance)
                    $projectCompanyAccount->balance += $diffAmount;
                    $projectCompanyAccount->save();
                    
                    // Update transaction
                    $transaction->amount = $newAmount;
                    $transaction->balance_after_transaction = $projectCompanyAccount->balance;
                    $transaction->save();
                    
                } else if ($mainCompanyExpenseAccount && $transaction->account_id === $mainCompanyExpenseAccount->id && $transaction->debit_credit === Transaction::DEBIT) {
                    // This is the main company expense transaction
                    $oldAmount = $transaction->amount;
                    $diffAmount = $newCommissionAmount - $oldAmount;
                    
                    // Update account balance (debit increases balance)
                    $mainCompanyExpenseAccount->balance += $diffAmount;
                    $mainCompanyExpenseAccount->save();
                    
                    // Update transaction
                    $transaction->amount = $newCommissionAmount;
                    $transaction->balance_after_transaction = $mainCompanyExpenseAccount->balance;
                    $transaction->save();
                    
                } else if ($responsiblePersonAccount && $transaction->account_id === $responsiblePersonAccount->id && $transaction->debit_credit === Transaction::CREDIT) {
                    // This is the responsible person commission transaction
                    $oldAmount = $transaction->amount;
                    $diffAmount = $newCommissionAmount - $oldAmount;
                    
                    // Update account balance (credit decreases balance)
                    $responsiblePersonAccount->balance -= $diffAmount;
                    $responsiblePersonAccount->save();
                    
                    // Update transaction
                    $transaction->amount = $newCommissionAmount;
                    $transaction->balance_after_transaction = $responsiblePersonAccount->balance;
                    $transaction->save();
                }
            }
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error updating task accounting: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        // Only process if task had a price
        if (!$task->price || $task->price <= 0) {
            return;
        }

        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Find the transaction group for this task
            $transactionGroup = TransactionGroup::where('name', "Task #{$task->id} - {$task->name}")->first();
            
            if (!$transactionGroup) {
                // No transaction group to delete
                return;
            }
            
            // Get all transactions in this group
            $transactions = Transaction::where('transaction_group_id', $transactionGroup->id)->get();
            
            if ($transactions->isEmpty()) {
                // No transactions to delete
                $transactionGroup->delete();
                return;
            }
            
            // Reverse each transaction
            foreach ($transactions as $transaction) {
                // Get the account
                $account = Account::find($transaction->account_id);
                
                if ($account) {
                    // Adjust account balance based on the transaction type
                    if ($transaction->debit_credit === Transaction::DEBIT) {
                        // For debit transactions, subtract from balance (reverse the increase)
                        $account->balance -= $transaction->amount;
                    } else {
                        // For credit transactions, add to balance (reverse the decrease)
                        $account->balance += $transaction->amount;
                    }
                    
                    // Save the updated account balance
                    $account->save();
                }
                
                // Delete the transaction
                $transaction->delete();
            }
            
            // Delete the transaction group
            $transactionGroup->delete();
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error deleting task accounting: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
