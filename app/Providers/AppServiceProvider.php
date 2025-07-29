<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Observers\CompanyObserver;
use App\Observers\TaskObserver;
use App\Observers\TransactionObserver;
use App\Observers\TransactionGroupObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Company::observe(CompanyObserver::class);
        Task::observe(TaskObserver::class);
        Transaction::observe(TransactionObserver::class);
        TransactionGroup::observe(TransactionGroupObserver::class);
    }
}
