<?php

namespace App\Providers;

use App\Repositories\Billboard\BillboardRepository;
use App\Repositories\Billboard\BillboardRepositoryInterface;
use App\Repositories\BillboardType\BillboardTypeRepository;
use App\Repositories\BillboardType\BillboardTypeRepositoryInterface;
use App\Repositories\BillboardFace\BillboardFaceRepository;
use App\Repositories\BillboardFace\BillboardFaceRepositoryInterface;
use App\Repositories\City\CityRepository;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class,
        );

        $this->app->bind(
            BillboardFaceRepositoryInterface::class,
            BillboardFaceRepository::class
        );

        $this->app->bind(
            BillboardRepositoryInterface::class,
            BillboardRepository::class
        );

        $this->app->bind(
            BillboardTypeRepositoryInterface::class,
            BillboardTypeRepository::class
        );

        $this->app->bind(
            CityRepositoryInterface::class,
            CityRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
