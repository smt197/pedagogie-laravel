<?php

namespace App\Providers;

use App\Models\BaseModel;
use App\Models\BaseModelInterface;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class FirebaseModelProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BaseModelInterface::class, BaseModel::class);

        $this->app->bind('user', function($app) {
            return new User();
        });
    }

    public function boot()
    {
        //
    }
}