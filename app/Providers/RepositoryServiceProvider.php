<?php

namespace App\Providers;

use App\Interfaces\BoardingHouseRepositoryInterface;
use App\Interfaces\CityRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Repositories\BoardingHouseRepositories;
use App\Repositories\CityRepositories;
use App\Repositories\CategoryRepositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            CityRepositoryInterface::class,
            CityRepositories::class,
        );

        $this->app->bind(
            BoardingHouseRepositoryInterface::class,
            BoardingHouseRepositories::class,
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepositories::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
