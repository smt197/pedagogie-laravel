<?php

namespace App\Services;

use App\Repositories\Promo\PromotionRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PromotionService
{
    protected $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function createPromotion(array $data)
    {
        Log::info('Creating promotion with data:', $data);

        // Validation des données
        if (empty($data['libelle']) || empty($data['date_debut'])) {
            Log::warning('Validation error: libelle or date_debut is empty.', $data);
            throw new Exception("Libellé et date de début sont obligatoires.");
        }

        // Vérifier si le libellé est unique dans Firebase
        $promotions = $this->promotionRepository->getAllPromotions();
        Log::info('Existing promotions:', $promotions);

        foreach ($promotions as $promotion) {
            if ($promotion['libelle'] === $data['libelle']) {
                Log::warning('Duplicate libelle found:', ['libelle' => $data['libelle']]);
                throw new Exception("Le libellé de la promotion doit être unique.");
            }
        }

        // Calcul de la durée ou de la date de fin si nécessaire
        if (!empty($data['duree'])) {
            $data['date_fin'] = Carbon::parse($data['date_debut'])->addMonths($data['duree'])->toDateString();
        } elseif (!empty($data['date_fin'])) {
            $data['duree'] = Carbon::parse($data['date_debut'])->diffInMonths(Carbon::parse($data['date_fin']));
        } else {
            Log::warning('Duration or end date not provided.', $data);
            throw new Exception("Il faut fournir soit la durée, soit la date de fin.");
        }

        // Par défaut, l'état de la promotion est 'Inactif'
        $data['etat'] = $data['etat'] ?? 'Inactif';

        // Vérifier l'état de la promotion
        Log::info('Promotion state before saving:', ['etat' => $data['etat']]);
        
        if ($data['etat'] === 'Actif') {
            foreach ($promotions as $promotion) {
                if ($promotion['etat'] === 'Actif') {
                    Log::warning('Active promotion already exists:', $promotion);
                    throw new Exception("Une seule promotion peut être active");
                }
            }
        }

        // Si l'état est 'Clôturer', vérifier que la date de fin est atteinte
        if ($data['etat'] === 'Clôturer') {
            if (Carbon::now()->lt(Carbon::parse($data['date_fin']))) {
                Log::warning('Promotion closure attempt before end date.', $data);
                throw new Exception("La promotion ne peut être clôturée que si la date de fin est atteinte.");
            }
        }

        // Vérifier et filtrer les référentiels actifs s'ils sont fournis
        if (isset($data['referentiels']) && is_array($data['referentiels'])) {
            $data['referentiels'] = array_filter($data['referentiels'], function ($referentiel) {
                return isset($referentiel['statut']) && $referentiel['statut'] === 'ACTIF';
            });
        } else {
            $data['referentiels'] = [];
        }

        Log::info('Final promotion data before creation:', $data);
        // Créer la promotion dans Firebase
        return $this->promotionRepository->createPromotion($data);
    }

    public function updatePromotion(string $id, array $data)
    {
        Log::info('Updating promotion ID: ' . $id, $data);
        return $this->promotionRepository->updatePromotion($id, $data);
    }

    public function deletePromotion(string $id)
    {
        Log::info('Deleting promotion ID: ' . $id);
        // Suppression logique (soft delete)
        $promotion = $this->promotionRepository->findPromotion($id);
        $promotion['deleted_at'] = now();
        return $this->promotionRepository->updatePromotion($id, $promotion);
    }

    public function getDeletedPromotions()
    {
        $deletedPromotions = $this->promotionRepository->getDeletedPromotions();
        Log::info('Fetching deleted promotions:', $deletedPromotions);
        return $deletedPromotions;
    }

    public function getAllPromotions()
    {
        $allPromotions = $this->promotionRepository->getAllPromotions();
        Log::info('Fetching all promotions:', $allPromotions);
        return $allPromotions;
    }

    public function updatePromotionReferentiels(string $id, array $data, $user)
    {
        Log::info('Updating referentiels for promotion ID: ' . $id, $data);
        $promotion = $this->promotionRepository->findPromotion($id);
        if (!$promotion) {
            Log::error('Promotion not found for ID: ' . $id);
            throw new Exception("Promotion not found.");
        }

        if (!isset($data['referentiels']) || !is_array($data['referentiels'])) {
            Log::warning('Referentiels data is missing or not an array.', $data);
            throw new Exception("Referentiels data is required and should be an array.");
        }

        // Check user role and permissions
        if ($user->role->nomRole === 'CM') {
            // CM can only remove empty referentials
            foreach ($data['referentiels'] as $referentiel) {
                if (isset($referentiel['remove']) && $referentiel['remove'] && count($referentiel['apprenants']) > 0) {
                    Log::warning('CM attempted to remove non-empty referentiel:', $referentiel);
                    throw new Exception("CM can only remove empty referentiels.");
                }
            }
        }

        // Update referentiels
        foreach ($data['referentiels'] as $referentiel) {
            if (isset($referentiel['remove']) && $referentiel['remove']) {
                // Soft delete logic
                $referentiel['deleted_at'] = now();
            } else {
                // Add or update referentiel
                $referentiel['etat'] = 'Actif';
            }
        }

        $promotion['referentiels'] = $data['referentiels'];
        Log::info('Final referentiels data for promotion ID: ' . $id, $promotion['referentiels']);

        return $this->promotionRepository->updatePromotion($id, $promotion);
    }

    public function updatePromotionEtat(string $id, string $etat, $user)
    {
        Log::info('Updating promotion state for ID: ' . $id . ' to ' . $etat);
        if ($user->role->nomRole !== 'Manager') {
            Log::warning('Unauthorized user role attempting to change promotion state:', ['userRole' => $user->role->nomRole]);
            throw new Exception("Only Manager can change the promotion status.");
        }

        $promotion = $this->promotionRepository->findPromotion($id);
        if (!$promotion) {
            Log::error('Promotion not found for ID: ' . $id);
            throw new Exception("Promotion not found.");
        }

        if ($etat === 'Actif') {
            $activePromotions = array_filter($this->promotionRepository->getAllPromotions(), function ($promotion) {
                return $promotion['etat'] === 'Actif';
            });
            if (count($activePromotions) > 0) {
                Log::warning('Attempting to activate a new promotion while another is active.');
                throw new Exception("Une seule promotion peut être active à la fois");
            }
        }

        if ($etat === 'Clôturer' && Carbon::now()->lt(Carbon::parse($promotion['date_fin']))) {
            Log::warning('Attempting to close promotion before end date.', $promotion);
            throw new Exception("The promotion can only be closed if the end date is reached.");
        }

        $promotion['etat'] = $etat;
        return $this->promotionRepository->updatePromotion($id, $promotion);
    }

    public function getActivePromotion()
    {
        Log::info('Fetching active promotion');
        $promotions = $this->promotionRepository->getAllPromotions();
        foreach ($promotions as $promotion) {
            if ($promotion['etat'] === 'Actif') {
                return $promotion;
            }
        }
        Log::warning('No active promotion found.');
        throw new Exception("No active promotion found.");
    }

    public function getPromotionReferentiels(string $id)
    {
        Log::info('Fetching referentiels for promotion ID: ' . $id);
        $promotion = $this->promotionRepository->findPromotion($id);
        if (!$promotion) {
            Log::error('Promotion not found for ID: ' . $id);
            throw new Exception("Promotion not found.");
        }

        $activeReferentiels = array_filter($promotion['referentiels'], function ($referentiel) {
            return $referentiel['etat'] === 'Actif';
        });

        Log::info('Active referentiels for promotion ID: ' . $id, $activeReferentiels);
        return $activeReferentiels;
    }
}
