<?php

namespace App\Http\Controllers;

use App\Services\ApprenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApprenantController extends Controller
{
    protected $apprenantService;

    public function __construct(ApprenantService $apprenantService)
    {
        $this->apprenantService = $apprenantService;
    }

    public function store(Request $request): JsonResponse
    {
        // Les validations sont déléguées au service
        try {
            $apprenant = $this->apprenantService->inscrireApprenant($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Apprenant inscrit avec succès',
                'data' => $apprenant,
            ], 201);
        } catch (\Exception $e) {
            // En cas d'erreur, renvoyer un message approprié
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getAllApprenants(Request $request) {
        $referentielId = $request->query('referentiel_id');
        try {
            $apprenants = $this->apprenantService->getApprenants($referentielId);

            return response()->json([
                'status' => 'success',
                'data' => $apprenants,
            ], 200);
        } catch (\Exception $e) {
            // En cas d'erreur, renvoyer un message approprié
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage(),
            ], 400);
        }
    }
    
}