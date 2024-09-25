<?php

namespace App\Models;

use Kreait\Firebase\Factory;

class ModelReferentiel
{
    protected $database;
    protected $auth;

    public function __construct()
    {
        $firebaseCredentials = base64_decode(env('FIREBASE_CREDENTIALS'));
        $firebase = (new Factory)
        ->withServiceAccount(json_decode($firebaseCredentials, true))
        ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

    $this->auth = $firebase->createAuth();
    $this->database = $firebase->createDatabase();
    }

    public function find()
    {
        $reference = $this->database->getReference('referentiels');
        return $reference->getSnapshot()->getValue();
    }

    public function create($data)
    {
        $reference = $this->database->getReference('referentiels')->push($data);
        return $reference->getKey(); // Retourne l'ID du nouveau référentiel
    }

    public function update($id, $data)
    {
        $reference = $this->database->getReference('referentiels/' . $id);
        $reference->update($data);
    }


    public function delete($id)
    {
        $reference = $this->database->getReference('referentiels/' . $id);
        $reference->remove();
    }



}
