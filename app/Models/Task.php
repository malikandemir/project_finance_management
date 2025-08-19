<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;

class Task extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'is_completed',
        'project_id',
        'created_by',
        'assigned_to',
        'price',
        'cost_percentage',
        'is_paid',
        'is_get_paid',
        'payment_account_id',
        'get_paid_account_id',
    ];
    
    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
        'is_paid' => 'boolean',
        'is_get_paid' => 'boolean',
    ];
    
    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Get the user who created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user assigned to the task.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get all comments for the task.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
    /**
     * Get all transaction groups for the task.
     */
    public function transactionGroups(): MorphMany
    {
        return $this->morphMany(TransactionGroup::class, 'transactionable');
    }
}
