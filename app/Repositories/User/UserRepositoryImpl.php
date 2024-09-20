<?php

namespace App\Repositories\User;

// use App\Facades\UserFacade as User;
use App\Facades\UserFacade;
use App\Repositories\User\IUserRepository;
use Illuminate\Support\Facades\Hash;

class UserRepositoryImpl implements IUserRepository
{

    //cree le user en locale
    public function create(array $data, string $firebaseUid)
    {
        return UserFacade::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'login' => $data['login'],
            'photo' => $data['photo'],
            'password' => Hash::make($data['password']),
            'fonction' => $data['fonction'],
            'statut' => $data['statut'],
            'role_id' => $data['role_id'],
            'firebase_uid' => $firebaseUid,
        ]);
    }

    public function update($uid, array $data)
    {
        $user = UserFacade::where('firebase_uid', $uid)->firstOrFail();

        // Si un nouveau mot de passe est fourni, le hacher
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }
}