<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class TransactionGroup extends Model implements Auditable
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \OwenIt\Auditing\Auditable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'group_date',
        'user_id',
        'transactionable_id',
        'transactionable_type',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group_date' => 'datetime',
    ];
    
    /**
     * Get the transactions associated with this group.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_group_id');
    }
    
    /**
     * Get the user that created this transaction group.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the parent transactionable model (Project or Task).
     */
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
