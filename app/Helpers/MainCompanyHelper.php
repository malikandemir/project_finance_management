<?php

namespace App\Helpers;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MainCompanyHelper
{
    /**
     * Get the main company.
     *
     * @return Company|null
     */
    public static function getMainCompany(): ?Model
    {
        // Cache the main company to avoid repeated database queries
        return Cache::remember('main_company', 60 * 60, function () {
            return Company::where('is_main', true)->first();
        });
    }

    /**
     * Check if a company is the main company.
     *
     * @param Company $company
     * @return bool
     */
    public static function isMainCompany(Company $company): bool
    {
        return $company->is_main;
    }

    /**
     * Clear the main company cache.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget('main_company');
    }
}
