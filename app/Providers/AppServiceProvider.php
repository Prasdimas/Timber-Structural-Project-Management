<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BuildingPartRepositoryInterface;
use App\Repositories\BuildingPartRepository;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\ProjectRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(BuildingPartRepositoryInterface::class, BuildingPartRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
