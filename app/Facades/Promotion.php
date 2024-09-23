<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Promotion extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'promotion';
    }
}
