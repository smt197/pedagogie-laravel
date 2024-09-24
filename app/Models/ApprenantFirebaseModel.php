<?php

namespace App\Models;

use Kreait\Firebase\Database;

class ApprenantFirebaseModel extends BaseFirebaseModel
{
    protected $table = 'apprenants';

    public function __construct(Database $database)
    {
        parent::__construct($database);
    }
}