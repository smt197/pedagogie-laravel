<?php
namespace App\Repositories\User;

use App\Models\User;

interface IUserRepository
{
    public function create(array $data, string $firebaseUid);
    public function update($uid, array $data);
}
