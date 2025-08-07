<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class HelpDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'category',
        'order',
        'parent_id',
        'language_code', // Added language_code field
    ];
    
    /**
     * Scope a query to only include documents in a specific language.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $languageCode
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInLanguage(Builder $query, string $languageCode): Builder
    {
        return $query->where('language_code', $languageCode);
    }

    /**
     * Get the parent help document.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(HelpDocument::class, 'parent_id');
    }

    /**
     * Get the child help documents.
     */
    public function children()
    {
        return $this->hasMany(HelpDocument::class, 'parent_id');
    }
}
