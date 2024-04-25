<?php

namespace App\Providers;

use App\Factories\FeeCalculationRuleFactory;
use App\Services\BankTransactionService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        BankTransactionService::class => BankTransactionService::class,
        FeeCalculationRuleFactory::class => FeeCalculationRuleFactory::class
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
