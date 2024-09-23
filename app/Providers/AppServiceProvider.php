<?php

namespace App\Providers;

use App\Models\ModelReferentiel;
use App\Models\PromotionFirebaseModel;
use App\Repositories\Promo\PromoInterfaceRepo;
use App\Repositories\Promo\PromotionRepository;
use App\Repositories\Referentiel\RefInterfaceRepo;
use App\Repositories\Referentiel\ReferentielRepo;
use App\Repositories\User\IUserRepository;
use App\Repositories\User\UserRepositoryImpl;
use Illuminate\Support\ServiceProvider;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceInterface;
use App\Services\PromotionService;
use App\Services\User\UserServiceImpl;
use App\Services\User\UserServiceInterface;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UriInterface::class, function ($app) {
            return new Uri();
        });

          // Assurez-vous d'avoir le binding Firebase
          $this->app->singleton(Database::class, function ($app) {
            return (new Factory)
                ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                ->withDatabaseUri(config('services.firebase.database_url')) // Pas besoin de withStorageBucket()
                ->createDatabase();
        });

        $this->app->bind(UserServiceInterface::class,UserServiceImpl::class);

        $this->app->bind(IUserRepository::class,UserRepositoryImpl::class);

        $this->app->bind(FirebaseServiceInterface::class,FirebaseService::class);

        $this->app->singleton(RefInterfaceRepo::class, function ($app) {
            return new ReferentielRepo(new ModelReferentiel());
        });

        $this->app->bind(PromoInterfaceRepo::class,PromotionRepository::class);















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
