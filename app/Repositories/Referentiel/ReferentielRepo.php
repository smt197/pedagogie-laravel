<?php

namespace App\Repositories\Referentiel;

use App\Facades\RefFacade;
use Illuminate\Support\Str;
use App\Repositories\Referentiel\RefInterfaceRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database;

class ReferentielRepo implements RefInterfaceRepo
{
    public function find()
    {
        $referentiels = RefFacade::findActive();
        return $this->formatReferentiels($referentiels);
    }

    public function finds($firebaseId = null)
    {
        if ($firebaseId) {
            $referentiel = RefFacade::findByFirebaseId($firebaseId);
            return $referentiel ? $this->formatReferentiel($firebaseId, $referentiel) : null;
        }
        $referentiels = RefFacade::findActive();
        return $this->formatReferentiels($referentiels);
    }


    public function create(array $data)
    {
        if (!RefFacade::isLibelleUnique($data['libelle'])) {
            throw new \Exception('Le libellé du référentiel est déjà pris. Veuillez choisir un autre libellé.');
        }
        
        $code = Str::upper(substr(str_shuffle('REF00'), 0, 4));
        $data['code'] = $code;
        $data['statut'] = $data['statut'] ?? 'INACTIF';
        $formattedData = $this->formatDataForCreate($data);
        return RefFacade::create($formattedData);
    }

    public function update($id, array $data)
    {
        $formattedData = $this->formatDataForUpdate($data);
        return RefFacade::update($id, $formattedData);
    }

    public function delete($firebaseId)
    {
        return RefFacade::softDelete($firebaseId);
    }

    public function findArchived()
    {
        $archivedReferentiels = RefFacade::findDeletedSoft();
        return $this->formatReferentiels($archivedReferentiels);
    }

    // public function delete($Id)
    // {
    //     return RefFacade::Delete($Id);
    // }

    protected function formatReferentiels($referentiels)
    {
        if (!is_array($referentiels)) {
            return [];
        }

        return array_map(function ($referentiel) {
            return [
                'id' => $referentiel['id'] ?? null,
                'code' => $referentiel['code'] ?? '',
                'libelle' => $referentiel['libelle'] ?? '',
                'description' => $referentiel['description'] ?? '',
                'photo' => $referentiel['photo'] ?? '',
                'statut' => $referentiel['statut'] ?? '',
                'competences' => $this->formatCompetences($referentiel['competences'] ?? []),
            ];
        }, $referentiels);
    }

    protected function formatReferentiel($firebaseId, $referentiel)
    {
        return [
            'id' => $firebaseId,
            'code' => $referentiel['code'] ?? '',
            'competences' => $this->formatCompetences($referentiel['competences'] ?? []),
        ];
    }

    protected function formatCompetences($competences)
    {
        return array_map(function ($competence) {
            return [
                'nom' => $competence['nom'] ?? '',
                'description' => $competence['description'] ?? '',
                'duree_acquisition' => $competence['duree_acquisition'] ?? '',
                'type' => $competence['type'] ?? '',
                'modules' => $this->formatModules($competence['modules'] ?? []),
            ];
        }, $competences);
    }

    protected function formatModules($modules)
    {
        return array_map(function ($module) {
            return [
                'nom' => $module['nom'] ?? '',
                'description' => $module['description'] ?? '',
                'duree_acquisition' => $module['duree_acquisition'] ?? '',
            ];
        }, $modules);
    }

    protected function formatDataForCreate($data)
    {
        return [
            'code' => $data['code'],
            'libelle' => $data['libelle'],
            'description' => $data['description'],
            'photo' => $data['photo'],
            'statut' => $data['statut'],
            'competences' => array_map(function ($competence) {
                return [
                    'nom' => $competence['nom'],
                    'description' => $competence['description'],
                    'duree_acquisition' => $competence['duree_acquisition'],
                    'type' => $competence['type'],
                    'modules' => array_map(function ($module) {
                        return [
                            'nom' => $module['nom'],
                            'description' => $module['description'],
                            'duree_acquisition' => $module['duree_acquisition'],
                        ];
                    }, $competence['modules'] ?? []),
                ];
            }, $data['competences'] ?? []),
        ];
    }

    protected function formatDataForUpdate($data)
    {
        $formattedData = [];
        if (isset($data['code'])) $formattedData['code'] = $data['code'];
        if (isset($data['libelle'])) $formattedData['libelle'] = $data['libelle'];
        if (isset($data['description'])) $formattedData['description'] = $data['description'];
        if (isset($data['photo'])) $formattedData['photo'] = $data['photo'];
        if (isset($data['competences'])) {
            $formattedData['competences'] = $this->formatCompetences($data['competences']);
        }
        return $formattedData;
    }

    public function filterByCompetences($id)
    {
        $referentiel = $this->finds($id);
        return [
            'id' => $referentiel['id'],
            'code' => $referentiel['code'],
            'competences' => array_map(function ($competence) {
                return [
                    'nom' => $competence['nom'],
                    'description' => $competence['description'],
                    'duree_acquisition' => $competence['duree_acquisition'],
                    'type' => $competence['type'],
                    'modules' => $competence['modules']
                ];
            }, $referentiel['competences'])
        ];
    }

    public function filterByModules($id)
    {
        $referentiel = $this->finds($id);
        $modules = [];
        foreach ($referentiel['competences'] as $competence) {
            $modules = array_merge($modules, $competence['modules']);
        }
        return [
            'id' => $referentiel['id'],
            'code' => $referentiel['code'],
            'modules' => $modules
        ];
    } 

    public function updateGeneralInfo($firebaseId, $data)
    {
        return RefFacade::update($firebaseId, [
            'libelle' => $data['libelle'],
            'description' => $data['description'],
            'photo' => $data['photo']
        ]);
    }

    public function addCompetence($firebaseId, $competence)
    {
        $database = app(Database::class);
        $reference = $database->getReference('referentiels/' . $firebaseId . '/competences');
        
        // Ajouter une nouvelle compétence
        return $reference->push([
            'nom' => $competence['nom'],
            'description' => $competence['description'],
            'duree_acquisition' => $competence['duree_acquisition'],
            'type' => $competence['type'],
            'modules' => $competence['modules']
        ])->getKey() != null;
    }

    public function deleteCompetence($firebaseId, $competenceData)
    {
        $database = app(Database::class);
        $competenceId = is_array($competenceData) ? $competenceData['id'] : $competenceData;


        if (!is_string($firebaseId) || !is_string($competenceId)) {
            Log::error("Validation échouée pour les identifiants", ['firebaseId' => $firebaseId, 'competenceId' => $competenceId]);
            throw new \InvalidArgumentException("Les identifiants doivent être des chaînes.");
        }
        
        $reference = $database->getReference('referentiels/' . $firebaseId . '/competences/' . $competenceId);
        
        try {
            $result = $reference->remove();
            return $result != null;
        } catch (\Throwable $th) {
            Log::error("Erreur lors de la suppression de la compétence", ['exception' => $th]);
            return false;
        }
    }

    public function deleteModule($firebaseId, $competenceId, $moduleId)
{
    $database = app(Database::class);

    Log::info("Tentative de suppression de module", ['firebaseId' => $firebaseId, 'competenceId' => $competenceId, 'moduleId' => $moduleId]);

    if (!is_string($firebaseId) || !is_string($competenceId) || !is_string($moduleId)) {
        Log::error("Validation échouée pour les identifiants", ['firebaseId' => $firebaseId, 'competenceId' => $competenceId, 'moduleId' => $moduleId]);
        throw new \InvalidArgumentException("Tous les identifiants doivent être des chaînes.");
    }

    $reference = $database->getReference('referentiels/' . $firebaseId . '/competences/' . $competenceId . '/modules/' . $moduleId);
    
    try {
        $result = $reference->remove();
        return $result != null;
    } catch (\Throwable $th) {
        Log::error("Erreur lors de la suppression du module", ['exception' => $th]);
        return false;
    }
}


}