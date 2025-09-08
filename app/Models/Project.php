<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\TheUniformChartOfAccount;
use App\Models\Transaction;

class Project extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'company_id',
        'created_by',
        'responsible_user_id',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    /**
     * Get the company that owns the project.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Get the user who created the project.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user responsible for the project.
     */
    public function responsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
    
    /**
     * Get the tasks for the project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
    /**
     * Get all comments for the project.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
    /**
     * Get all transaction groups for the project.
     */
    public function transactionGroups(): MorphMany
    {
        return $this->morphMany(TransactionGroup::class, 'transactionable');
    }
    
    /**
     * Calculate the total price of the project based on tasks
     * 
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->tasks()->sum('price') ?: 0;
    }
    
    /**
     * Calculate the total amount paid for the project
     * 
     * @return float
     */
    public function getTotalPaid(): float
    {
        // Find the account receivable transactions related to this project
        $uniformAccount120 = TheUniformChartOfAccount::where('number', '120')->first();
        
        if (!$uniformAccount120) {
            return 0;
        }
        
        // Get all transaction groups related to this project
        $transactionGroupIds = $this->transactionGroups()->pluck('id');
        
        if ($transactionGroupIds->isEmpty()) {
            return 0;
        }
        
        // Get all credit transactions for account receivable (payments received)
        // Credit transactions decrease the accounts receivable balance
        $totalPaid = Transaction::whereIn('transaction_group_id', $transactionGroupIds)
            ->whereHas('account', function($query) use ($uniformAccount120) {
                $query->where('account_uniform_id', $uniformAccount120->id);
            })
            ->where('debit_credit', Transaction::CREDIT)
            ->sum('amount');
            
        return $totalPaid ?: 0;
    }
    
    /**
     * Check if the project has been fully paid
     * 
     * @return bool
     */
    public function isFullyPaid(): bool
    {
        $totalPrice = $this->getTotalPrice();
        $totalPaid = $this->getTotalPaid();
        
        // If there's no price, consider it not paid
        if ($totalPrice <= 0) {
            return false;
        }
        
        // Consider it paid if the paid amount is equal or greater than the total price
        return $totalPaid >= $totalPrice;
    }
}
