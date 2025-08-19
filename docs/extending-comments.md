# Extending the Commenting System to Other Models

This document explains how to extend the commenting system to additional models beyond the current implementation (Companies, Projects, and Tasks).

## Overview

The commenting system uses Laravel's polymorphic relationships to allow comments to be associated with any model in the application. This makes it easy to extend the commenting functionality to new models.

## Steps to Add Comments to a New Model

### 1. Update the Model

Add the `morphMany` relationship to your model class:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class YourModel extends Model
{
    // ... existing code ...
    
    /**
     * Get all comments for this model.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
```

### 2. Update the Resource

Add the CommentsRelationManager to your Filament resource:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YourModelResource\Pages;
use App\Filament\RelationManagers\CommentsRelationManager;
// ... other imports ...

class YourModelResource extends Resource
{
    // ... existing code ...
    
    public static function getRelations(): array
    {
        return [
            // ... existing relation managers ...
            CommentsRelationManager::class,
        ];
    }
    
    // ... rest of the resource ...
}
```

### 3. Testing

After adding the relationship and updating the resource, you should be able to:

1. View existing comments on the model's view/edit pages
2. Add new comments to the model
3. Edit or delete comments (based on permissions)

## How It Works

The commenting system uses Laravel's polymorphic relationships with the following structure:

- The `Comment` model has `commentable_id` and `commentable_type` fields that store the ID and class name of the related model.
- The `morphMany` relationship in each model defines how to retrieve comments for that model.
- The `CommentsRelationManager` handles displaying and managing comments in the Filament UI.

This design allows for a flexible commenting system that can be easily extended to any model in the application.
