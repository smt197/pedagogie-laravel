<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->role->nomRole === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut afficher un article spécifique.
     */
    public function view(User $user)
    {
        return $user->role->nomRole === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut créer un article.
     */
    public function create(User $user)
    {
        return $user->role->nomRole === 'ADMIN';
    }

    
}
