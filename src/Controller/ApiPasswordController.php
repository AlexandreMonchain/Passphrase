<?php
// src/Controller/ApiPasswordController.php

namespace App\Controller;

use App\Service\CsvCacheService;
use App\Service\PasswordGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ApiPasswordController extends AbstractController
{
    #[Route('/api/passwords', name: 'api_generate_passwords', methods: ['GET'])]
    public function getGeneratedPasswords(Request $request, CsvCacheService $csvCacheService, PasswordGeneratorService $passwordGeneratorService): JsonResponse
    {
        $validSeparateurs    = ['random', '-', '_', ' ', '*', '/', '+'];
        $validCaracteresSpec = ['random', 'none', '$', '!', '#', '?', '-', '+', '@', ',', ';', ':', '*'];

        $nbMots           = max(2, min(7,  (int) $request->query->get('nb_mots', 2)));
        $longueurMinimale = max(8, min(50, (int) $request->query->get('longueur_minimale', 12)));
        $longueurNombre   = max(0, min(5,  (int) $request->query->get('longueur_nombre', 2)));
        $count            = max(1, min(20, (int) $request->query->get('count', 1)));

        $separateur = $request->query->get('separateur', 'random');
        if (!in_array($separateur, $validSeparateurs, true)) {
            $separateur = 'random';
        }

        $caractereSpecial = $request->query->get('caractere_special', 'random');
        if (!in_array($caractereSpecial, $validCaracteresSpec, true)) {
            $caractereSpecial = 'random';
        }

        $majusculeDebut    = filter_var($request->query->get('majuscule_debut', true), FILTER_VALIDATE_BOOLEAN);
        $majusculeAleatoire = filter_var($request->query->get('majuscule_aleatoire', false), FILTER_VALIDATE_BOOLEAN);
        $caracteresAccentues = filter_var($request->query->get('caracteres_accentues', true), FILTER_VALIDATE_BOOLEAN);

        // Récupérer les mots depuis le service CSV
        $mots = $csvCacheService->getCsvData();

        $data = [
            'nb_mots'             => $nbMots,
            'longueur_minimale'   => $longueurMinimale,
            'separateur'          => $separateur,
            'majuscule_debut'     => $majusculeDebut,
            'majuscule_aleatoire' => $majusculeAleatoire,
            'longueur_nombre'     => $longueurNombre,
            'caractere_special'   => $caractereSpecial,
            'caracteres_accentues'=> $caracteresAccentues,
        ];

        // Utilisation du service pour générer les mots de passe sans entropie
        $passwords = $passwordGeneratorService->generatePasswords($data, $mots, $count, false);

        return new JsonResponse([
            'passwords' => $passwords,
        ]);
    }
}