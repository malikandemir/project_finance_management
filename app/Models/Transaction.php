<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'amount',
        'debit_credit',
        'account_id',
        'user_id',
        'transaction_group_id',
        'balance_after_transaction',
        'transaction_date',
        'description',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after_transaction' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];
    
    /**
     * Debit credit constants
     */
    const DEBIT = 1;
    const CREDIT = 2;
    
    /**
     * Get the account that owns the transaction.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    
    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the transaction group that this transaction belongs to.
     */
    public function transactionGroup(): BelongsTo
    {
        return $this->belongsTo(TransactionGroup::class, 'transaction_group_id');
    }
}
