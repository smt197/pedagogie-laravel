<?php

namespace App\Services;

interface FirebaseServiceInterface
{
    public function createUser($data);
    public function storeUserDetails($uid, $details);
    public function findAll();
    public function updateUser($uid, $data);
}