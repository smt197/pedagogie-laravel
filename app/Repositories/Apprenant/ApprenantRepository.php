<?php

namespace App\Repositories\Apprenant;

use App\Facades\Apprenant;
use Kreait\Firebase\Database; // Assurez-vous d'importer la classe Database


class ApprenantRepository
{

    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    public function createApprenant(array $data)
    {
        return Apprenant::create($data);
    }

    public function findApprenant(string $id)
    {
        return Apprenant::find($id);
    }

    public function updateApprenant(string $id, array $data)
    {
        return Apprenant::update($id, $data);
    }

    public function deleteApprenant(string $id)
    {
        return Apprenant::delete($id);
    }

    public function getAllApprenants()
    {
        return Apprenant::all();
    }

    // Nouvelle méthode pour récupérer les compétences par référentiel depuis Firebase
    public function getCompetencesByReferentielId(string $referentielId)
    {
        // Accéder à la collection de référentiels dans Firebase
        $referentielsRef = $this->database->getReference('referentiels/' . $referentielId);
        $referentiel = $referentielsRef->getValue();

        if ($referentiel && isset($referentiel['competences'])) {
            return $referentiel['competences']; // Retourne les compétences
        }

        return []; // Retourne un tableau vide si aucune compétence n'est trouvée
    }

    public function associateToPromotion(string $apprenantId, string $referentielId, string $promotionId)
    {
        // Récupérer les détails de la promotion pour vérifier la validité du référentiel
        $promotionRef = $this->database->getReference('promotions/' . $promotionId);
        $promotion = $promotionRef->getValue();

        // Vérification de l'existence de la promotion et des référentiels
        if (!$promotion || !isset($promotion['referentiels'])) {
            throw new \Exception("Promotion non trouvée ou ne contient aucun référentiel.");
        }

        // Vérification si le référentiel est présent dans la promotion
        $referentiels = $promotion['referentiels'];
        // dd($referentiels);
        $referentielFound = false;

        foreach ($referentiels as $referentiel) {
            if ($referentiel['code'] === $referentielId && $referentiel['etat'] === 'Actif') {
                $referentielFound = true;
                break;
            }
        }

        if (!$referentielFound) {
            throw new \Exception("Le référentiel n'est pas actif ou n'existe pas dans cette promotion.");
        }

        // Mise à jour du document de l'apprenant avec les ID du référentiel et de la promotion
        $this->database->getReference('apprenants/' . $apprenantId)->update([
            'referentiel_id' => $referentielId,
            'promotion_id' => $promotionId,
            'date_inscription' => now()->toDateString(), // Optionnel : ajouter la date d'inscription
            'statut' => 'INSCRIT' // Optionnel : mettre à jour le statut de l'apprenant
        ]);
    }

    public function listerApprenantsPromotionActive($referentielId = null)
    {
        // Récupérer toutes les promotions actives
        $promotionsActives = $this->database->getReference('promotions')
            ->orderByChild('etat')
            ->equalTo('Actif')
            ->getValue();

        // Si aucune promotion active trouvée
        if (!$promotionsActives) {
            return [];
        }

        $apprenantsActifs = [];

        // Récupérer les apprenants pour chaque promotion active
        foreach ($promotionsActives as $promotionId => $promotion) {
            $apprenants = $this->database->getReference('apprenants')
                ->orderByChild('promotion_id')
                ->equalTo($promotionId)
                ->getValue();

            if ($apprenants) {
                foreach ($apprenants as $apprenant) {
                    // Si un referentiel_id est fourni, on filtre les apprenants par ce référentiel
                    if ($referentielId) {
                        if (isset($apprenant['referentiel_id']) && $apprenant['referentiel_id'] === $referentielId) {
                            $apprenantsActifs[] = $apprenant;
                        }
                    } else {
                        // Sinon, on ajoute tous les apprenants de la promotion active
                        $apprenantsActifs[] = $apprenant;
                    }
                }
            }
        }

        return $apprenantsActifs;
    }


    public function associateUserToApprenant($apprenantId, $userId)
    {
        $this->database->getReference('apprenants/' . $apprenantId)->update([
            'user_id' => $userId,
        ]);
    }
}
