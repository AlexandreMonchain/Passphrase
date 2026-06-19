<?php
// src/Controller/HomeController.php
namespace App\Controller;

use App\Service\CsvCacheService;
use App\Service\PasswordGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PasswordGenerationFormType;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CsvCacheService $csvCacheService, PasswordGeneratorService $passwordGeneratorService): Response
    {
        $defaults = [
            'nb_mots'             => 2,
            'separateur'          => 'random',
            'majuscule_debut'     => true,
            'majuscule_aleatoire' => false,
            'longueur_nombre'     => 2,
            'caractere_special'   => 'random',
            'longueur_minimale'   => 12,
            'caracteres_accentues'=> true,
            'nb_resultats'        => 10,
        ];

        $userPreferencesCookie = $request->cookies->get('user_preferences');
        $userPreferences = $userPreferencesCookie
            ? array_merge($defaults, json_decode($userPreferencesCookie, true) ?? [])
            : $defaults;

        // Clamp des valeurs du cookie pour éviter qu'un cookie forgé déclenche une génération excessive
        $userPreferences['nb_mots']          = max(2, min(7,  (int) ($userPreferences['nb_mots'] ?? 2)));
        $userPreferences['longueur_minimale'] = max(8, min(50, (int) ($userPreferences['longueur_minimale'] ?? 12)));
        $userPreferences['longueur_nombre']   = max(0, min(5,  (int) ($userPreferences['longueur_nombre'] ?? 2)));
        $userPreferences['nb_resultats']      = in_array((int) ($userPreferences['nb_resultats'] ?? 10), [6, 10, 20])
            ? (int) $userPreferences['nb_resultats'] : 10;

        // Appel du formulaire dans src\Form et passage du tableau associatif pour le préremplir
        $form = $this->createForm(PasswordGenerationFormType::class, $userPreferences);
        $form->handleRequest($request);

        // Récupérer les mots du CSV depuis le cache
        $mots = $csvCacheService->getCsvData();

        // Tableau des mots de passe générés avec entropie
        $passwordsWithEntropy = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $passwordsWithEntropy = $passwordGeneratorService->generatePasswords($data, $mots, $data['nb_resultats'] ?? 10);

            // Créer un cookie pour sauvegarder les préférences utilisateur
            $isSecure = $request->isSecure();
            
            $cookie = new Cookie(
                'user_preferences',
                json_encode($data),
                time() + (3600 * 24 * 30),  // Expiration dans 30 jours
                '/',                        // Path
                null,                       // Domaine
                $isSecure,                  // Sécurisé (adapté à l'environnement)
                true,                       // HTTPOnly
                false,                      // Raw
                Cookie::SAMESITE_LAX        // SameSite policy
            );

            $response = new Response();
            $response->headers->setCookie($cookie);

            // Réponse Ajax si nécessaire
            if ($request->isXmlHttpRequest()) {
                return $this->render('home/_generated_passwords.html.twig', [
                    'generated_passwords' => $passwordsWithEntropy,
                ], $response);
            }

            // Sinon, rendre la page avec les mots de passe générés
            return $this->render('home/index.html.twig', [
                'form' => $form->createView(),
                'generated_passwords' => $passwordsWithEntropy,
            ], $response);
        }

        // Si le formulaire n'est pas soumis, générer les mots de passe par défaut
        $passwordsWithEntropy = $passwordGeneratorService->generatePasswords($userPreferences, $mots, $userPreferences['nb_resultats'] ?? 10);

        // Rendre la vue initiale avec les mots de passe par défaut
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'generated_passwords' => $passwordsWithEntropy,
        ]);
    }
}