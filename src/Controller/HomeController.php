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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Form\PasswordGenerationFormType;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CsvCacheService $csvCacheService, PasswordGeneratorService $passwordGeneratorService): Response
    {
        // Récupérer les préférences utilisateur depuis le cookie
        $userPreferencesCookie = $request->cookies->get('user_preferences');
        if ($userPreferencesCookie) {
            $userPreferences = json_decode($userPreferencesCookie, true);
        } else {
            // Valeur par defaut si pas de cookie
            $userPreferences = [
                'nb_mots' => 2,
                'separateur' => 'random',
                'majuscule_debut' => true,
                'majuscule_aleatoire' => false,
                'longueur_nombre' => 2,
                'caractere_special' => 'random',
                'longueur_minimale' => 12,
                'caracteres_accentues' => true,
            ];
        }

        // Appel du formulaire dans src\Form et passage du tableau associatif pour le préremplir
        $form = $this->createForm(PasswordGenerationFormType::class, $userPreferences);
        $form->handleRequest($request);

        // Récupérer les mots du CSV depuis le cache
        $mots = $csvCacheService->getCsvData();

        // Tableau des mots de passe générés avec entropie
        $passwordsWithEntropy = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Utilisation du service pour générer les mots de passe
            $passwordsWithEntropy = $passwordGeneratorService->generatePasswords($data, $mots);

            // Créer un cookie pour sauvegarder les préférences utilisateur
            $cookie = new Cookie(
                'user_preferences',
                json_encode($data),
                time() + (3600 * 24 * 30),  // Expiration dans 30 jours
                '/',                        // Path
                null,                       // Domaine
                true,                       // Sécurisé
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
        $passwordsWithEntropy = $passwordGeneratorService->generatePasswords($userPreferences, $mots);

        // Rendre la vue initiale avec les mots de passe par défaut
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'generated_passwords' => $passwordsWithEntropy,
        ]);
    }
}
?>