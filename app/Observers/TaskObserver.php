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
                'name' => "Task #{$task->id} - {$task->title}",
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
            
            // Note: Transactions between main company and responsible person are now moved to the completeTask method
            // and will be processed when the task is completed
            
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
     * @param int $userId
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
            // Make sure account_group_id is explicitly set
            $account = Account::create([
                'account_name' => $accountName,
                'balance' => 0,
                'account_uniform_id' => $uniformAccount->id,
                'account_group_id' => $accountGroup->id, // Ensure this is always set
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
        // Check if the task status is being changed
        if ($task->isDirty('status')) {
            // Handle status change to completed
            if ($task->status === 'completed') {
                $this->handleTaskCompletion($task);
                return;
            }
            
            // Handle status change from completed to another status
            if ($task->getOriginal('status') === 'completed' && $task->status !== 'completed') {
                $this->reverseTaskCompletion($task);
                return;
            }
        }
        
        // Check if the task is being marked as completed via is_completed flag
        if ($task->isDirty('is_completed') && $task->is_completed) {
            $this->handleTaskCompletion($task);
            return;
        }
        
        // Check if the task is being unmarked as completed via is_completed flag
        if ($task->isDirty('is_completed') && !$task->is_completed && $task->getOriginal('is_completed')) {
            $this->reverseTaskCompletion($task);
            return;
        }
        
        // Check if price was changed
        if (!$task->isDirty('price') || !$task->price) {
            return;
        }

        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Find the transaction group for this task
            $transactionGroup = TransactionGroup::where('name', "Task #{$task->id} - {$task->title}")->first();
            
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
            
            // New amount
            $newAmount = $task->price;
            
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
                }
                
                // Note: We don't update the main company expense and responsible person transactions here
                // as those will be created only when the task is completed via the completeTask method
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
    
    /**
     * Process transactions when a task is completed.
     * This handles the transactions between main company and responsible person
     * that should only happen after task completion.
     * 
     * @param Task $task
     * @return bool
     */
    public function completeTask(Task $task): bool
    {
        // Check if task is already completed
        if ($task->is_completed) {
            throw new \Exception('This task is already marked as completed');
        }
        
        // Check if task has a price
        if (!$task->price || $task->price <= 0) {
            // Just mark as completed without financial transactions
            $task->is_completed = true;
            $task->save();
            return true;
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create a transaction group for completion transactions
            $transactionGroup = TransactionGroup::create([
                'name' => "Task Completion #{$task->id} - {$task->title}",
                'description' => "Completion Transactions for Task #{$task->id} in Project #{$task->project_id}",
                'group_date' => now(),
                'user_id' => $task->project->responsible_user_id,
            ]);
            
            // Get the main company
            $mainCompany = MainCompanyHelper::getMainCompany();
            if (!$mainCompany) {
                throw new \Exception('No main company found for accounting operations');
            }
            
            // Get the responsible user
            $responsibleUser = $task->assignedUser;
            if (!$responsibleUser) {
                throw new \Exception('No responsible user found for the task');
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
                $description = "Task Completion: Project #{$task->project_id}, Task #{$task->id}, {$mainCompany->name} (Expense)";
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
                $description = "Task Completion: Project #{$task->project_id}, Task #{$task->id}, {$responsibleUser->name} (Commission)";
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
            
            // Mark task as completed
            $task->is_completed = true;
            $task->save();
            
            // Commit transaction
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error processing task completion accounting: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Process the "get paid" action for a task.
     * 
     * @param Task $task
     * @param int $mainCompanyAccountId The selected main company account (100 or 102)
     * @return bool
     */
    public function getPaid(Task $task, int $mainCompanyAccountId): bool
    {
        // Check if task has already been paid
        if ($task->is_get_paid) {
            throw new \Exception('This task has already been paid');
        }
        
        // Check if task has a price
        if (!$task->price || $task->price <= 0) {
            throw new \Exception('Task has no price or price is zero');
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create a transaction group for all related transactions
            $transactionGroup = TransactionGroup::create([
                'name' => "Get Paid for Task #{$task->id} - {$task->title}",
                'description' => "Get Paid Transactions for Task #{$task->id} in Project #{$task->project_id}",
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
            
            // Get the selected main company account
            $mainCompanyAccount = Account::find($mainCompanyAccountId);
            if (!$mainCompanyAccount) {
                throw new \Exception('Selected main company account not found');
            }
            
            // Verify the account belongs to the main company and is 100 or 102
            $uniformAccount = $mainCompanyAccount->uniformChartOfAccount;
            if (!$uniformAccount || !in_array($uniformAccount->number, ['100', '102'])) {
                throw new \Exception('Selected account is not a valid main company account (100 or 102)');
            }
            
            // Get the project company account (120)
            $projectCompanyAccount = $this->getOrCreateAccount($projectCompany->name, '120', $projectCompany->owner_id);
            
            // Amount to use for transactions
            $amount = $task->price;
            
            // Create debit transaction for main company account (100/102) - Asset increases with debit
            $newMainBalance = $mainCompanyAccount->balance + $amount;
            $description = "Get Paid: Project #{$task->project_id}, Task #{$task->id}, {$mainCompany->name}";
            $this->createTransaction(
                $amount,
                Transaction::DEBIT,
                $mainCompanyAccount->id,
                $task->project->responsible_user_id,
                $newMainBalance,
                substr($description, 0, 120),
                $transactionGroup->id
            );
            
            // Update main company account balance
            $mainCompanyAccount->balance = $newMainBalance;
            $mainCompanyAccount->save();
            
            // Create credit transaction for project company (120) - Receivable decreases with credit
            $newProjectBalance = $projectCompanyAccount->balance - $amount;
            $description = "Get Paid: Project #{$task->project_id}, Task #{$task->id}, {$projectCompany->name}";
            $this->createTransaction(
                $amount,
                Transaction::CREDIT,
                $projectCompanyAccount->id,
                $task->project->responsible_user_id,
                $newProjectBalance,
                substr($description, 0, 120),
                $transactionGroup->id
            );
            
            // Update project company account balance
            $projectCompanyAccount->balance = $newProjectBalance;
            $projectCompanyAccount->save();
            
            // Update task with payment information
            $task->is_get_paid = true;
            $task->get_paid_account_id = $mainCompanyAccountId;
            $task->save();
            
            // Commit transaction
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error processing task get paid: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Process the "pay" action for a task.
     * 
     * @param Task $task
     * @param int $mainCompanyAccountId The selected main company account (100 or 102)
     * @return bool
     */
    public function pay(Task $task, int $mainCompanyAccountId): bool
    {
        // Check if task has already been paid
        if ($task->is_paid) {
            throw new \Exception('This task has already been paid');
        }
        
        // Check if task has a price
        if (!$task->price || $task->price <= 0) {
            throw new \Exception('Task has no price or price is zero');
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create a transaction group for all related transactions
            $transactionGroup = TransactionGroup::create([
                'name' => "Pay for Task #{$task->id} - {$task->title}",
                'description' => "Pay Transactions for Task #{$task->id} in Project #{$task->project_id}",
                'group_date' => now(),
                'user_id' => $task->project->responsible_user_id,
            ]);
            
            // Get the main company
            $mainCompany = MainCompanyHelper::getMainCompany();
            if (!$mainCompany) {
                throw new \Exception('No main company found for accounting operations');
            }
            
            // Get the selected main company account
            $mainCompanyAccount = Account::find($mainCompanyAccountId);
            if (!$mainCompanyAccount) {
                throw new \Exception('Selected main company account not found');
            }
            
            // Verify the account belongs to the main company and is 100 or 102
            $uniformAccount = $mainCompanyAccount->uniformChartOfAccount;
            if (!$uniformAccount || !in_array($uniformAccount->number, ['100', '102'])) {
                throw new \Exception('Selected account is not a valid main company account (100 or 102)');
            }
            
            // Get the responsible user
            $responsibleUser = $task->assignedUser;
            if (!$responsibleUser) {
                throw new \Exception('No responsible user found for the task');
            }
            
            // Get the responsible person account (320)
            $responsiblePersonAccount = $this->getOrCreateAccount($responsibleUser->name, '320', $responsibleUser->id);
            
            // Calculate payment amount based on cost_percentage or revenue_percentage
            $percentage = $task->cost_percentage > 0 ? $task->cost_percentage : $responsibleUser->revenue_percentage;
            $amount = $task->price * ($percentage / 100);
            
            // If no percentage is set or amount is zero, throw an exception
            if ($percentage <= 0 || $amount <= 0) {
                throw new \Exception('No valid cost or revenue percentage found for payment calculation');
            }
            
            // Create credit transaction for main company account (100/102) - Asset decreases with credit
            $newMainBalance = $mainCompanyAccount->balance - $amount;
            $description = "Pay: Project #{$task->project_id}, Task #{$task->id}, {$mainCompany->name} ({$percentage}%)";
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
            
            // Create debit transaction for responsible person account (320) - Liability decreases with debit
            $newPersonBalance = $responsiblePersonAccount->balance + $amount;
            $description = "Pay: Project #{$task->project_id}, Task #{$task->id}, {$responsibleUser->name} ({$percentage}%)";
            $this->createTransaction(
                $amount,
                Transaction::DEBIT,
                $responsiblePersonAccount->id,
                $responsibleUser->id,
                $newPersonBalance,
                substr($description, 0, 120),
                $transactionGroup->id
            );
            
            // Update responsible person account balance
            $responsiblePersonAccount->balance = $newPersonBalance;
            $responsiblePersonAccount->save();
            
            // Update task with payment information
            $task->is_paid = true;
            $task->payment_account_id = $mainCompanyAccountId;
            $task->save();
            
            // Commit transaction
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error processing task payment: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Handle task completion when status changes to 'completed'
     * Creates a transaction group and financial transactions for the task
     *
     * @param Task $task
     * @return bool
     */
    public function handleTaskCompletion(Task $task): bool
    {
        // Check if task has a price
        if (!$task->price || $task->price <= 0) {
            // Just mark as completed without financial transactions
            $task->is_completed = true;
            $task->save();
            return true;
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Create a transaction group for completion transactions
            $transactionGroup = TransactionGroup::create([
                'name' => "Status Completion #{$task->id} - {$task->title}",
                'description' => "Status Completion Transactions for Task #{$task->id} in Project #{$task->project_id}",
                'group_date' => now(),
                'user_id' => $task->project->responsible_user_id,
                'transactionable_id' => $task->id,
                'transactionable_type' => get_class($task),
            ]);
            
            // Get the main company
            $mainCompany = MainCompanyHelper::getMainCompany();
            if (!$mainCompany) {
                throw new \Exception('No main company found for accounting operations');
            }
            
            // Get the responsible user
            $responsibleUser = $task->assignedUser;
            if (!$responsibleUser) {
                throw new \Exception('No responsible user found for the task');
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
                $description = "Status Completion: Project #{$task->project_id}, Task #{$task->id}, {$mainCompany->name} (Expense)";
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
                $description = "Status Completion: Project #{$task->project_id}, Task #{$task->id}, {$responsibleUser->name} (Commission)";
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
            
            // Mark task as completed
            $task->is_completed = true;
            $task->save();
            
            // Commit transaction
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error processing task status completion accounting: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Handle task status change from 'completed' to another status
     * Reverses the financial transactions created during completion
     *
     * @param Task $task
     * @return bool
     */
    public function reverseTaskCompletion(Task $task): bool
    {
        // Check if task has a price
        if (!$task->price || $task->price <= 0) {
            // Just mark as not completed without financial transactions
            $task->is_completed = false;
            $task->save();
            return true;
        }
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Find all transaction groups related to this task's completion
            $transactionGroups = TransactionGroup::where('transactionable_id', $task->id)
                ->where('transactionable_type', get_class($task))
                ->where('name', 'like', 'Status Completion #%')
                ->get();
            
            if ($transactionGroups->isEmpty()) {
                // No transaction groups found, just update the task status
                $task->is_completed = false;
                $task->save();
                DB::commit();
                return true;
            }
            
            // Create a new transaction group for the reversal
            $reversalGroup = TransactionGroup::create([
                'name' => "Status Reversal #{$task->id} - {$task->title}",
                'description' => "Status Reversal Transactions for Task #{$task->id} in Project #{$task->project_id}",
                'group_date' => now(),
                'user_id' => $task->project->responsible_user_id,
                'transactionable_id' => $task->id,
                'transactionable_type' => get_class($task),
            ]);
            
            // Process each transaction group
            foreach ($transactionGroups as $group) {
                $transactions = Transaction::where('transaction_group_id', $group->id)->get();
                
                foreach ($transactions as $transaction) {
                    // Get the account
                    $account = Account::find($transaction->account_id);
                    
                    if (!$account) {
                        continue;
                    }
                    
                    // Create a reversal transaction (opposite debit/credit)
                    $oppositeType = $transaction->debit_credit === Transaction::DEBIT ? 
                        Transaction::CREDIT : Transaction::DEBIT;
                    
                    // Calculate new balance
                    $newBalance = $oppositeType === Transaction::DEBIT ?
                        $account->balance + $transaction->amount :
                        $account->balance - $transaction->amount;
                    
                    // Create reversal transaction
                    $description = "Status Reversal: Project #{$task->project_id}, Task #{$task->id}, {$account->account_name}";
                    $this->createTransaction(
                        $transaction->amount,
                        $oppositeType,
                        $account->id,
                        $transaction->user_id,
                        $newBalance,
                        substr($description, 0, 120),
                        $reversalGroup->id
                    );
                    
                    // Update account balance
                    $account->balance = $newBalance;
                    $account->save();
                }
            }
            
            // Mark task as not completed
            $task->is_completed = false;
            $task->save();
            
            // Commit transaction
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error processing task status reversal accounting: ' . $e->getMessage());
            throw $e;
        }
    }
}
