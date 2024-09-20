<?php
namespace App\Services\User;


interface UserServiceInterface
{
    public function createUser(array $data);
    public function canCreateUser($userRole, $newUserRole);
    public function index($role = null);
    public function updateUser($uid, array $data);
}
