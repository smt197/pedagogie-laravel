<?php
namespace App\Repositories\Promo;

use App\Models\PromotionFirebaseModel;
use App\Repositories\Promo\PromoInterfaceRepo;

class PromotionRepository implements PromoInterfaceRepo
{
    protected $promotionModel;

    public function __construct(PromotionFirebaseModel $promotionModel)
    {
        $this->promotionModel = $promotionModel;
    }

    public function createPromotion(array $data)
    {
        return $this->promotionModel->create($data);
    }

    public function findPromotion(string $id)
    {
        return $this->promotionModel->find($id);
    }

    public function updatePromotion(string $id, array $data)
    {
        return $this->promotionModel->update($id, $data);
    }

    public function deletePromotion(string $id)
    {
        return $this->promotionModel->delete($id);
    }

    public function getAllPromotions()
    {
        return $this->promotionModel->all();
    }

    public function getDeletedPromotions()
    {
        $promotions = $this->getAllPromotions();
        return array_filter($promotions, function ($promotion) {
            return isset($promotion['deleted_at']);
        });
    }





}
