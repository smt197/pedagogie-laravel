<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Apprenant extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'apprenant';
    }
}