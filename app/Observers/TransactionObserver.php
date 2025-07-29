<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // No specific action needed when a transaction is created
        // Account balance is already updated in the TaskObserver
    }

    /**
     * Flag to prevent recursive updates
     */
    private static $updating = false;

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Prevent recursive updates
        if (self::$updating) {
            return;
        }
        
        // Check if amount or debit_credit was changed
        if (!$transaction->wasChanged('amount') && !$transaction->wasChanged('debit_credit')) {
            return;
        }

        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Get the account
            $account = Account::find($transaction->account_id);
            
            if (!$account) {
                throw new \Exception("Account with ID {$transaction->account_id} not found");
            }
            
            // If amount changed, update account balance
            if ($transaction->isDirty('amount')) {
                $oldAmount = $transaction->getOriginal('amount');
                $newAmount = $transaction->amount;
                $diffAmount = $newAmount - $oldAmount;
                
                // Update account balance based on transaction type
                if ($transaction->debit_credit === Transaction::DEBIT) {
                    // For debit transactions, add the difference to balance
                    $account->balance += $diffAmount;
                } else {
                    // For credit transactions, subtract the difference from balance
                    $account->balance -= $diffAmount;
                }
            }
            
            // If debit_credit changed, reverse the old effect and apply the new one
            if ($transaction->isDirty('debit_credit')) {
                $oldType = $transaction->getOriginal('debit_credit');
                $amount = $transaction->amount;
                
                // Reverse the old effect
                if ($oldType === Transaction::DEBIT) {
                    // If it was a debit, subtract from balance
                    $account->balance -= $amount;
                } else {
                    // If it was a credit, add to balance
                    $account->balance += $amount;
                }
                
                // Apply the new effect
                if ($transaction->debit_credit === Transaction::DEBIT) {
                    // If it's now a debit, add to balance
                    $account->balance += $amount;
                } else {
                    // If it's now a credit, subtract from balance
                    $account->balance -= $amount;
                }
            }
            
            // Update the transaction's balance_after_transaction field without triggering observers again
            $transaction->balance_after_transaction = $account->balance;
            $transaction->saveQuietly();
            
            // Save the account
            $account->save();
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error updating transaction: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Get the account
            $account = Account::find($transaction->account_id);
            
            if (!$account) {
                // If account doesn't exist, nothing to update
                DB::commit();
                return;
            }
            
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
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error deleting transaction: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Get the account
            $account = Account::find($transaction->account_id);
            
            if (!$account) {
                throw new \Exception("Account with ID {$transaction->account_id} not found");
            }
            
            // Adjust account balance based on the transaction type
            if ($transaction->debit_credit === Transaction::DEBIT) {
                // For debit transactions, add to balance
                $account->balance += $transaction->amount;
            } else {
                // For credit transactions, subtract from balance
                $account->balance -= $transaction->amount;
            }
            
            // Update the transaction's balance_after_transaction field
            $transaction->balance_after_transaction = $account->balance;
            $transaction->save();
            
            // Save the updated account balance
            $account->save();
            
            // Commit transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error restoring transaction: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        // Use the same logic as the deleted method
        $this->deleted($transaction);
    }
}
