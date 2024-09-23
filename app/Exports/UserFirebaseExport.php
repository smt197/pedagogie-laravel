<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserFirebaseExport implements FromCollection
{
   
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }
    public function collection()
    {
        return $this->users->map(function ($user){
            return [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'login' => $user->login,
                'fonction' => $user->fonction,
                'statut' => $user->statut,
                'role_id' => $user->role_id
            ];
        });
    }

    public function headings(): array
    {
        return ['Nom', 'Prénom', 'Email', 'Login', 'Fonction', 'Statut', 'Role_id'];
    }





}
