<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Account extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_name',
        'balance',
        'account_group_id',
        'account_uniform_id',
        'user_id',
    ];
    
    /**
     * Get the account group that owns the account.
     */
    public function accountGroup(): BelongsTo
    {
        return $this->belongsTo(AccountGroup::class, 'account_group_id');
    }
    
    /**
     * Get the uniform chart of account that owns the account.
     */
    public function uniformChartOfAccount(): BelongsTo
    {
        return $this->belongsTo(TheUniformChartOfAccount::class, 'account_uniform_id');
    }
    
    /**
     * Get the user that owns the account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the transactions for the account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }
}
