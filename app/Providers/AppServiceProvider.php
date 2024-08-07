<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CrudRepositoryInterface;
use App\Repositories\CityRepository;
use App\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CrudRepositoryInterface::class, CityRepository::class);
        $this->app->bind(CrudRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
