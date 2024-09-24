<?php

namespace App\Providers;

use App\Models\ApprenantFirebaseModel;
use App\Models\BaseModel;
use App\Models\BaseModelInterface;
use App\Models\ModelReferentiel;
use App\Models\PromotionFirebaseModel;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Database;

class FirebaseModelProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BaseModelInterface::class, BaseModel::class);

        $this->app->bind('user', function($app) {
            return new User();
        });

        $this->app->bind('referentiel', function($app) {
            return new ModelReferentiel();
        });

        $this->app->singleton('promotion', function ($app) {
            return new PromotionFirebaseModel($app->make(Database::class));
        });

        $this->app->singleton('apprenant', function ($app) {
            return new ApprenantFirebaseModel($app->make(Database::class));
        });
    }

    public function boot()
    {
        //
    }
}