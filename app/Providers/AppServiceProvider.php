<?php

namespace App\Providers;

use App\Repositories\User\IUserRepository;
use App\Repositories\User\UserRepositoryImpl;
use Illuminate\Support\ServiceProvider;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceInterface;
use App\Services\User\UserServiceImpl;
use App\Services\User\UserServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(UserServiceInterface::class,UserServiceImpl::class);

        $this->app->bind(IUserRepository::class,UserRepositoryImpl::class);

        $this->app->bind(FirebaseServiceInterface::class,FirebaseService::class);











        $this->app->singleton('FirebaseClient', function () {
            return new FirebaseService();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
