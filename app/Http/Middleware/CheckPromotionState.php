<?php

namespace App\Http\Middleware;

use App\Repositories\Promo\PromotionRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPromotionState
{

    protected $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $id): Response
    {

        $promotion = $this->promotionRepository->findPromotion($id);

        if ($promotion && $promotion['etat'] === 'Clôturé') {
            Log::warning('Attempt to modify a closed promotion', ['id' => $id]);
            return response()->json(['message' => 'Cette promotion est clôturée et ne peut plus être modifiée.'], 403);
        }
        return $next($request);
    }
}
