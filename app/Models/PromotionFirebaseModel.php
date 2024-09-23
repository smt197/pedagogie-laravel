<?php
namespace App\Models;

use Kreait\Firebase\Database;

class PromotionFirebaseModel extends BaseFirebaseModel
{
    protected $table = 'promotions'; // Collection 'promotions' dans Firebase

    public function __construct(Database $database)
    {
        parent::__construct($database);
    }
}