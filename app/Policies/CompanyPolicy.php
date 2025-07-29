<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;
    
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Super-admin bypasses all permission checks
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        return null; // Fall through to the specific policy method
    }
    
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_companies');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        // Check if user has general permission to view companies
        if ($user->hasPermissionTo('view_company')) {
            // If they're a company owner, check if they own this company
            if ($user->hasRole('company-owner')) {
                // Check if user is associated with this company
                return $company->users()->where('users.id', $user->id)->exists();
            }
            
            // Project managers and team members can view companies they're associated with
            if ($user->hasAnyRole(['project-manager', 'team-member'])) {
                // Check if user is associated with any project in this company
                return $company->projects()
                    ->whereHas('users', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    })
                    ->exists();
            }
            
            return true; // Other roles with view_company permission can view all companies
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_company');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        // Check if user has general permission to edit companies
        if ($user->hasPermissionTo('edit_company')) {
            // If they're a company owner, check if they own this company
            if ($user->hasRole('company-owner')) {
                // Check if user is associated with this company
                return $company->users()->where('users.id', $user->id)->exists();
            }
            
            return true; // Other roles with edit_company permission can edit all companies
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        // Check if user has general permission to delete companies
        if ($user->hasPermissionTo('delete_company')) {
            // If they're a company owner, check if they own this company
            if ($user->hasRole('company-owner')) {
                // Check if user is associated with this company
                return $company->users()->where('users.id', $user->id)->exists();
            }
            
            return true; // Other roles with delete_company permission can delete all companies
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->hasPermissionTo('restore_company');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->hasPermissionTo('force_delete_company');
    }
    
    /**
     * Determine whether the user can manage company users.
     */
    public function manageUsers(User $user, Company $company): bool
    {
        // Check if user has general permission to manage company users
        if ($user->hasPermissionTo('manage_company_users')) {
            // If they're a company owner, check if they own this company
            if ($user->hasRole('company-owner')) {
                // Check if user is associated with this company
                return $company->users()->where('users.id', $user->id)->exists();
            }
            
            return true; // Other roles with manage_company_users permission can manage all company users
        }
        
        return false;
    }
}
