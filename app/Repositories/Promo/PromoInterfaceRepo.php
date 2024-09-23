<?php

namespace App\Repositories\Promo;


interface PromoInterfaceRepo
{
    public function createPromotion(array $data);
    public function findPromotion(string $id );
    public function updatePromotion(string $id, array $data);
    public function deletePromotion(string $id);
    public function getAllPromotions();
    public function getDeletedPromotions();
}
