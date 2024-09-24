<?php

namespace App\Services;

use App\Repositories\Apprenant\ApprenantRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendMailJob;
use App\Repositories\User\IUserRepository; 
use App\Services\User\UserServiceInterface; 
use Illuminate\Support\Facades\Hash;


class ApprenantService
{
    protected $apprenantRepository;
    protected $qrCodeService;
    protected $pdfService;
    protected $userService;


    public function __construct(
        ApprenantRepository $apprenantRepository,
        QrCodeService $qrCodeService,
        PdfService $pdfService,
        UserServiceInterface $userService,
        )
    {
        $this->apprenantRepository = $apprenantRepository;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
        $this->userService = $userService;
    }

    public function inscrireApprenant(array $data)
    {
        // Validation des données
        $validator = Validator::make($data, [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'adresse' => 'required|string',
            'telephone' => 'required|string',
            'email' => 'required|email',
            'login' => 'required|string',
            'photo' => 'required|string',
            'referentiel_id' => 'required|string',
            'promotion_id' => 'required|string',
            'tuteur.nom' => 'required|string',
            'tuteur.telephone' => 'required|string',
            'documents.carte_identite' => 'required|string',
            'documents.diplome' => 'required|string',
            'documents.visite_medicale' => 'required|string',
            'documents.extrait_naissance' => 'required|string',
            'documents.casier_judiciaire' => 'required|string',
            'referentiels' => 'array',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        // Recherche de l'utilisateur existant à partir de l'email
        $user = $this->userService->findUserByEmail($data['email']);
        if (!$user) {
            throw new \Exception('Utilisateur non trouvé avec cet email.');
        }

        // Génération du matricule
        $data['matricule'] = 'APP-' . Str::upper(Str::random(4));
        
        // Génération du QR Code et du PDF
        $qrCodeBase64 = $this->qrCodeService->generateBase64QrCode(json_encode($data));
        $pdfFilePath = $this->pdfService->generateQrCodePdf($qrCodeBase64);
        
        // Création de l'apprenant
        $apprenant = $this->apprenantRepository->createApprenant($data);

         // Associer l'apprenant à l'utilisateur existant
         $this->apprenantRepository->associateUserToApprenant($apprenant['matricule'], $user->uid);

        // Association de l'apprenant avec le référentiel et la promotion
        $this->apprenantRepository->associateToPromotion($apprenant['matricule'], $data['referentiel_id'], $data['promotion_id']);

        // Envoi de l'email d'inscription
        $this->envoyerMailInscription($apprenant, $pdfFilePath);

        return $apprenant;
    }

    // Méthode pour envoyer l'email d'inscription avec gestion des pièces jointes
    protected function envoyerMailInscription($apprenant, $pdfFilePath)
    {
        $mailData = [
            'email' => $apprenant['email'],
            'login' => $apprenant['email'],
            'password' => 'Passer@123', // À remplacer par un mot de passe généré ou sécurisé
        ];
        SendMailJob::dispatch($apprenant['email'], $pdfFilePath, $mailData);
    }

    // Méthode pour récupérer la liste des apprenants
    public function getApprenants($referentielId)
    {
        return $this->apprenantRepository->getAllApprenants($referentielId);
    }

    // Récupérer les compétences par référentiel
    public function getCompetencesByReferentiel($referentielId)
    {
        return $this->apprenantRepository->getCompetencesByReferentielId($referentielId);
    }

    
}
