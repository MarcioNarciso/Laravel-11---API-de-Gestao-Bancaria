<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        \App\Interfaces\Services\BankTransactionServiceInterface::class => \App\Services\BankTransactionService::class,
        \App\Interfaces\Factories\FeeCalculationRuleFactoryInterface::class => \App\Factories\FeeCalculationRuleFactory::class,
        \App\Interfaces\Repositories\AccountRepositoryInterface::class => \App\Repositories\AccountRepository::class,
        \App\Interfaces\Repositories\BankTransactionRepositoryInterface::class => \App\Repositories\BankTransactionRepository::class
    ];

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
        //
    }
}
