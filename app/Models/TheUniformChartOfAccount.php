<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TheUniformChartOfAccount extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'tr_name',
        'en_name',
    ];
    
    /**
     * Get the accounts for the uniform chart of account.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'account_uniform_id');
    }
}
