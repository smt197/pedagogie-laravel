<?php

namespace App\Http\Controllers;

use App\Facades\RefFacade;
use App\Http\Requests\StoreReferentielRequest;
use App\Repositories\Referentiel\RefInterfaceRepo;
use Illuminate\Http\Request;

class ReferentielController extends Controller
{

    protected $referentielRepo;

    public function __construct(RefInterfaceRepo $referentielRepo)
    {
        $this->referentielRepo = $referentielRepo;
    }
    public function index(Request $request)
    {
        $status = $request->query('statut', 'ACTIF');
        
        switch ($status) {
            case 'INACTIF':
                $referentiels = RefFacade::findInactive();
                break;
            case 'ARCHIVER':
                $referentiels = RefFacade::findArchived();
                break;
            case 'ACTIF':
            default:
                $referentiels = RefFacade::findActive();
                break;
        }

        return response()->json($referentiels);
    }


    public function store(StoreReferentielRequest $request)
    {
        $validated = $request->validated();

        try {
            $referentiel = $this->referentielRepo->create($validated);
            return response()->json(['message' => 'Référentiel créé avec succès', 'referentiel' => $referentiel], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du référentiel', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($firebaseId, Request $request)
    {
        $filter = $request->query('filter');
    
        switch ($filter) {
            case 'competences':
                $result = $this->referentielRepo->filterByCompetences($firebaseId);
                break;
            case 'modules':
                $result = $this->referentielRepo->filterByModules($firebaseId);
                break;
            default:
                $result = $this->referentielRepo->finds($firebaseId);
                break;
        }
    
        if ($result === null) {
            return response()->json(['error' => 'Référentiel non trouvé'], 404);
        }
    
        return response()->json($result);
    }


    public function destroy($id)
    {
        $result = $this->referentielRepo->delete($id);
        
        if ($result) {
            return response()->json(['message' => 'Référentiel supprimé avec succès'], 200);
        } else {
            return response()->json(['error' => 'Référentiel non trouvé ou impossible à supprimer'], 404);
        }
    }

    public function archived()
    {
        $archivedReferentiels = $this->referentielRepo->findArchived();
        return response()->json($archivedReferentiels);
    }


    public function update($id, Request $request)
    {
        $data = $request->all();

        // Mise à jour des informations générales du référentiel
        if (isset($data['generalInfo'])) {
            $result = $this->referentielRepo->updateGeneralInfo($id, $data['generalInfo']);
            if (!$result) {
                return response()->json(['error' => 'Échec de la mise à jour des informations générales'], 404);
            }
        }

        // Ajouter une compétence
        if (isset($data['addCompetence'])) {
            $result = $this->referentielRepo->addCompetence($id, $data['addCompetence']);
            if (!$result) {

                return response()->json(["error => 'Échec de l'ajout de la compétence"], 404);
            }
        }

        // Supprimer une compétence
        if (isset($data['deleteCompetence'])) {
            $result = $this->referentielRepo->deleteCompetence($id, $data['deleteCompetence']);
            if (!$result) {
                return response()->json(['error' => 'Échec de la suppression de la compétence'], 404);
            }
        }

        // Supprimer un module
        if (isset($data['deleteModule'])) {
            $competenceId = isset($data['deleteCompetence']['id']) ? $data['deleteCompetence']['id'] : null;
            $moduleId = isset($data['deleteModule']['id']) ? $data['deleteModule']['id'] : null;
            if ($competenceId && $moduleId) {
                $result = $this->referentielRepo->deleteModule($id, $competenceId, $moduleId);
                if (!$result) {
                    return response()->json(['error' => 'Échec de la suppression du module'], 404);
                }
            } else {
                return response()->json(['error' => 'Identifiants de compétence ou module manquants'], 400);
            }
        }

        return response()->json(['message' => 'Référentiel mis à jour avec succès'], 200);
    }


}