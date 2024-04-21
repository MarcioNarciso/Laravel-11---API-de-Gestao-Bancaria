<?php

namespace App\Providers;

use App\Factories\RegraCalculoTaxaFactory;
use App\Services\TransacaoBancariaService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        TransacaoBancariaService::class => TransacaoBancariaService::class,
        RegraCalculoTaxaFactory::class => RegraCalculoTaxaFactory::class
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
