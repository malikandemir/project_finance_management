<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionGroupObserver
{
    /**
     * Handle the TransactionGroup "created" event.
     */
    public function created(TransactionGroup $transactionGroup): void
    {
        // No specific action needed when a transaction group is created
    }

    /**
     * Handle the TransactionGroup "updated" event.
     */
    public function updated(TransactionGroup $transactionGroup): void
    {
        // No specific action needed when a transaction group is updated
    }

    /**
     * Handle the TransactionGroup "deleted" event.
     * When a transaction group is deleted, all associated transactions should be deleted
     * and account balances should be updated accordingly.
     */
    public function deleted(TransactionGroup $transactionGroup): void
    {
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Get all transactions in this group
            $transactions = Transaction::where('transaction_group_id', $transactionGroup->id)->get();
            
            if ($transactions->isEmpty()) {
                // No transactions to delete
                DB::commit();
                return;
            }
            
            // First, get all unique accounts affected by these transactions
            $accountIds = $transactions->pluck('account_id')->unique()->toArray();
            $accounts = Account::whereIn('id', $accountIds)->get()->keyBy('id');
            
            // Reverse each transaction and update account balances
            foreach ($transactions as $transaction) {
                // Get the account
                $account = $accounts->get($transaction->account_id);
                
                if ($account) {
                    // Adjust account balance based on the transaction type
                    if ($transaction->debit_credit === Transaction::DEBIT) {
                        // For debit transactions, subtract from balance (reverse the increase)
                        $account->balance -= $transaction->amount;
                    } else {
                        // For credit transactions, add to balance (reverse the decrease)
                        $account->balance += $transaction->amount;
                    }
                }
                
                // Delete the transaction without triggering observers
                // Use DB::table to bypass the model and observers
                DB::table('transactions')->where('id', $transaction->id)->delete();
            }
            
            // Save all updated account balances
            foreach ($accounts as $account) {
                $account->save();
            }
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error deleting transaction group: ' . $e->getMessage());
        }
    }

    /**
     * Handle the TransactionGroup "restored" event.
     */
    public function restored(TransactionGroup $transactionGroup): void
    {
        // No specific action needed when a transaction group is restored
    }

    /**
     * Handle the TransactionGroup "force deleted" event.
     */
    public function forceDeleted(TransactionGroup $transactionGroup): void
    {
        // Use the same logic as the deleted method
        $this->deleted($transactionGroup);
    }
}
