<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Kreait\Firebase\Contract\Database;

class RefFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'referentiel';
    }

    public static function isLibelleUnique(string $libelle)
    {
        $referentiels = self::find();
        $existingLibelles = array_column($referentiels, 'libelle');

        return !in_array($libelle, $existingLibelles);
    }

    public static function findActive()
    {
        $referentiels = self::find();
        return array_filter($referentiels, function ($referentiel) {
            return isset($referentiel['statut']) && $referentiel['statut'] === 'ACTIF';
        });
    }

    public static function findInactive()
    {
        $referentiels = self::find();
        return array_filter($referentiels, function ($referentiel) {
            return isset($referentiel['statut']) && $referentiel['statut'] === 'INACTIF';

        });
    }

    public static function findArchived()
    {
        $referentiels = self::find();
        return array_filter($referentiels, function ($referentiel) {
            return isset($referentiel['statut']) && $referentiel['statut'] === 'ARCHIVER';
        });
    }

    public static function findByFirebaseId($firebaseId)
    {
        $database = app(Database::class);
        $reference = $database->getReference('referentiels/' . $firebaseId);
        $snapshot = $reference->getValue();

        return $snapshot ?: null;
    }

    public static function softDelete($firebaseId)
    {
        $database = app(Database::class);
        $reference = $database->getReference('referentiels/' . $firebaseId);
        
        // Vérifier si le référentiel existe
        $snapshot = $reference->getValue();
        if (!$snapshot) {
            return false;
        }
        
        // Mettre à jour le statut à 'SUPPRIME'
        $reference->update([
            'statut' => 'SUPPRIME'
        ]);
        
        return true;
    }

    public static function findDeletedSoft()
    {
        $database = app(Database::class);
        $reference = $database->getReference('referentiels');
        $snapshot = $reference->getValue();

        return array_filter($snapshot, function($item) {
            return isset($item['statut']) && $item['statut'] === 'SUPPRIME';
        }) ?: [];
    }

    // update a reference
    public static function update($id, array $data)
    {
        $database = app(Database::class);
        $reference = $database->getReference('referentiels/'. $id);
        return $reference->update($data);
    }

}
