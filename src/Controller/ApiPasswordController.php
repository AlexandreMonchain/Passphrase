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
        // Récupérer les paramètres passés dans la requête GET ou utiliser les valeurs par défaut de userPreferences
        $nbMots = $request->query->get('nb_mots', 2); 
        $longueurMinimale = $request->query->get('longueur_minimale', 12); 
        $separateur = $request->query->get('separateur', 'random'); 
        $majusculeDebut = $request->query->get('majuscule_debut', true); 
        $majusculeAleatoire = $request->query->get('majuscule_aleatoire', false); 
        $longueurNombre = $request->query->get('longueur_nombre', 2); 
        $caractereSpecial = $request->query->get('caractere_special', 'random'); 

        // Le nombre de mots de passe à générer (par défaut : 1)
        $count = $request->query->get('count', 1);

        // Récupérer les mots depuis le service CSV
        $mots = $csvCacheService->getCsvData();

        // Paramètres de génération de mot de passe basés sur les valeurs par défaut ou les valeurs passées
        $data = [
            'nb_mots' => $nbMots,
            'longueur_minimale' => $longueurMinimale,
            'separateur' => $separateur,
            'majuscule_debut' => filter_var($majusculeDebut, FILTER_VALIDATE_BOOLEAN),
            'majuscule_aleatoire' => filter_var($majusculeAleatoire, FILTER_VALIDATE_BOOLEAN),
            'longueur_nombre' => $longueurNombre,
            'caractere_special' => $caractereSpecial,
        ];

        // Utilisation du service pour générer les mots de passe sans entropie
        $passwords = $passwordGeneratorService->generatePasswords($data, $mots, $count, false);

        // Retourner la réponse JSON avec les mots de passe générés
        return new JsonResponse([
            'passwords' => $passwords,
        ]);
    }
}

?>