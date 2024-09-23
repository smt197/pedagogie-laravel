<?php

namespace App\Http\Controllers;

use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function createPromotion(Request $request): JsonResponse
    {
        try {
            $promotion = $this->promotionService->createPromotion($request->all());
            return response()->json($promotion, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updatePromotion(string $id, Request $request): JsonResponse
    {
        try {
            $promotion = $this->promotionService->updatePromotion($id, $request->all());
            return response()->json($promotion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deletePromotion(string $id): JsonResponse
    {
        try {
            $promotion = $this->promotionService->deletePromotion($id);
            return response()->json($promotion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function getDeletedPromotions(): JsonResponse
    {
        try {
            $promotions = $this->promotionService->getDeletedPromotions();
            return response()->json($promotions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllPromotions(): JsonResponse
    {
        try {
            $promotions = $this->promotionService->getAllPromotions();
            return response()->json($promotions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function updatePromotionReferentiels(string $id, Request $request): JsonResponse
    {
        try {
            $promotion = $this->promotionService->updatePromotionReferentiels($id, $request->all(), auth()->user());
            return response()->json($promotion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updatePromotionEtat(string $id, Request $request): JsonResponse
    {
        try {
            $promotion = $this->promotionService->updatePromotionEtat($id, $request->etat, auth()->user());
            return response()->json($promotion, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


            public function getActivePromotion(): JsonResponse
        {
            try {
                $promotion = $this->promotionService->getActivePromotion();
                return response()->json($promotion, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }


        public function getPromotionReferentiels(string $id): JsonResponse
            {
                try {
                    $referentiels = $this->promotionService->getPromotionReferentiels($id);
                    return response()->json($referentiels, 200);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 400);
                }
            }


}
