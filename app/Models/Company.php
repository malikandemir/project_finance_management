<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'logo',
        'description',
        'is_active',
        'is_main',
        'created_by',
        'owner_id',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the creator of the company.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the owner of the company.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    /**
     * Get the projects for the company.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
