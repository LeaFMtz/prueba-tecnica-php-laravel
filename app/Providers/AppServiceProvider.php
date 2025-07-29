<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\ParticipantRepositoryInterface;
use App\Repositories\ParticipantRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ParticipantRepositoryInterface::class,
            ParticipantRepository::class
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
