<?php

namespace App\Controller;

use App\Service\CsvCacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ApiPasswordController extends AbstractController
{
    #[Route('/api/passwords', name: 'api_generate_passwords', methods: ['GET'])]
    public function getGeneratedPasswords(Request $request, CsvCacheService $csvCacheService): JsonResponse
    {
        // Récupérer les paramètres passés dans la requête GET ou utiliser les valeurs par défaut de userPreferences
        $nbMots = $request->query->get('nb_mots', 2); // Par défaut : 2 mots
        $longueurMinimale = $request->query->get('longueur_minimale', 12); // Par défaut : longueur minimale 12
        $separateur = $request->query->get('separateur', 'random'); // Par défaut : random
        $majusculeDebut = $request->query->get('majuscule_debut', true); // Par défaut : true (majuscule au début)
        $majusculeAleatoire = $request->query->get('majuscule_aleatoire', false); // Par défaut : false (pas de majuscule aléatoire)
        $longueurNombre = $request->query->get('longueur_nombre', 2); // Par défaut : 2 chiffres à la fin
        $caractereSpecial = $request->query->get('caractere_special', 'random'); // Par défaut : caractère spécial aléatoire

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

        // Générer les mots de passe
        $passwords = $this->generatePasswordsForApi($data, $mots, $count);

        // Retourner la réponse JSON avec les mots de passe générés
        return new JsonResponse([
            'passwords' => $passwords,
        ]);
    }

    // Méthode privée pour générer les mots de passe
    private function generatePasswordsForApi(array $data, array $mots, int $count): array
    {
        $caracteresSpeciaux = ['$', '*', '!', ':', ';', ',', '?', '#'];
        $separateurs = ['-', '_', ' ', '*', '/', '+'];

        $passwords = [];
        for ($i = 0; $i < $count; $i++) {
            do {
                $passwordParts = [];

                for ($j = 0; $j < $data['nb_mots']; $j++) {
                    $mot = $mots[array_rand($mots)];

                    if ($data['majuscule_debut']) {
                        $mot = ucfirst($mot);
                    }

                    if ($data['majuscule_aleatoire']) {
                        $pos = random_int(0, strlen($mot) - 1);
                        $mot[$pos] = strtoupper($mot[$pos]);
                    }

                    $passwordParts[] = $mot;
                }

                $separateur = $data['separateur'] === 'random'
                    ? $separateurs[array_rand($separateurs)]
                    : $data['separateur'];

                $password = implode($separateur, $passwordParts);

                if ($data['longueur_nombre'] > 0) {
                    $nombreAleatoire = str_pad(random_int(0, (10 ** $data['longueur_nombre']) - 1), $data['longueur_nombre'], '0', STR_PAD_LEFT);
                    $password .= $nombreAleatoire;
                }

                if ($data['caractere_special'] === 'random') {
                    $password .= $caracteresSpeciaux[array_rand($caracteresSpeciaux)];
                } elseif ($data['caractere_special'] !== 'none') {
                    $password .= $data['caractere_special'];
                }

            } while (strlen($password) < $data['longueur_minimale']);

            $passwords[] = $password;
        }

        return $passwords;
    }
}
