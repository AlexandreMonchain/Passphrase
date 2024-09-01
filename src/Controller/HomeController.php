<?php
namespace App\Controller;

use App\Service\CsvCacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, CsvCacheService $csvCacheService): Response
    {
        // Créer le formulaire de personnalisation avec des valeurs par défaut
        $form = $this->createFormBuilder()
            ->add('nb_mots', ChoiceType::class, [
                'label' => 'Nombre de mots dans le mot de passe',
                'choices' => [                    
                    '2 mots' => 2,
                    '3 mots' => 3,
                    '4 mots' => 4,
                    '5 mots' => 5,
                    '6 mots' => 6,
                    '7 mots' => 7,
                ],
                'data' => 2,
            ])
            ->add('longueur_minimale', IntegerType::class, [
                'label' => 'Longueur minimale du mot de passe',
                'attr' => ['min' => 8, 'max' => 50],
                'data' => 12,
            ])
            ->add('separateur', ChoiceType::class, [
                'label' => 'Séparateur entre les mots',
                'choices' => [
                    'Aléatoire' => 'random',
                    'Tiret (-)' => '-',
                    'Underscore (_)' => '_',
                    'Espace ( )' => ' ',
                    'Etoile (*)' => '*',
                    'Slash (/)' => '/',
                    'Plus (+)' => '+',
                ],
                'data' => 'random',
            ])
            ->add('majuscule_debut', CheckboxType::class, [
                'label' => 'Majuscule au début de chaque mot',
                'required' => false,
                'data' => true,
            ])
            ->add('majuscule_aleatoire', CheckboxType::class, [
                'label' => 'Majuscule à un endroit aléatoire dans chaque mot',
                'required' => false,
            ])
            ->add('longueur_nombre', ChoiceType::class, [
                'label' => 'Nombre de chiffres à la fin du mot de passe',
                'choices' => [
                    '0 chiffre' => 0,
                    '1 chiffre' => 1,
                    '2 chiffres' => 2,
                    '3 chiffres' => 3,
                    '4 chiffres' => 4,
                    '5 chiffres' => 5,                    
                ],
                'data' => 2,
            ])
            ->add('caractere_special', ChoiceType::class, [
                'label' => 'Caractère spécial à la fin',
                'choices' => [
                    'Aléatoire' => 'random',
                    'Aucun' => 'none',
                    'Dollar ($)' => '$',
                    'Point d\'exclamation (!)' => '!',
                    'Croisillon (#)' => '#',
                    'Point d\'interrogation (?)' => '?',
                    'Tiret (-)' => '-',
                    'Plus (+)' => '+',
                    'Arobase (@)' => '@',
                    'Virgule (,)' => ',',
                    'Point virugule (;)' => ';',
                    'Deux Points (:)' => ':',
                    'Etoile (*)' => '*',
                ],
                'data' => 'random',
            ])
            ->add('generate', SubmitType::class, ['label' => 'Générer les mots de passe'])
            ->getForm();

        $form->handleRequest($request);

        // Récupérer les mots du CSV depuis le cache
        $mots = $csvCacheService->getCsvData();

        // Tableau des mots de passe générés avec entropie
        $passwordsWithEntropy = [];

        // Logique pour générer des mots de passe si le formulaire n'a pas encore été soumis
       if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $passwordsWithEntropy = $this->generatePasswordsWithEntropy($data, $mots);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('home/_generated_passwords.html.twig', [
                'generated_passwords' => $passwordsWithEntropy,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'generated_passwords' => $passwordsWithEntropy,
        ]);
    }

    // Modification de la méthode pour inclure l'entropie des mots de passe
    private function generatePasswordsWithEntropy(array $data, array $mots): array
    {
        $caracteresSpeciaux = ['$', '*', '!', ':', ';', ',', '?', '#'];
        $separateurs = ['-', '_', ' ', '*', '/', '+'];

        $passwordsWithEntropy = [];
        for ($i = 0; $i < 10; $i++) {
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

                if ($data['separateur'] === 'random') {
                    $separateur = $separateurs[array_rand($separateurs)];
                } else {
                    $separateur = $data['separateur'];
                }

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

            // Calcul de l'entropie du mot de passe
            $entropy = $this->calculateEntropy($password);

            // Ajouter le mot de passe et son entropie dans le tableau
            $passwordsWithEntropy[] = [
                'password' => $password,
                'entropy' => $entropy,
                'class' => $this->getBootstrapEntropyClass($entropy) // Ajout de la classe Bootstrap en fonction de l'entropie
            ];
        }

        return $passwordsWithEntropy;
    }

    // Fonction pour calculer l'entropie d'un mot de passe
    private function calculateEntropy(string $password): float
    {
        $charset_size = 0;
        $has_lowercase = false;
        $has_uppercase = false;
        $has_digits = false;
        $has_special_chars = false;

        // Combinaison des caractères spéciaux et séparateurs dans une seule expression régulière
        $special_chars_pattern = '/[\$\*\!\:\;\,\?\#]/';
        $separators_pattern = '/[\-_ \*\/\+]/';


        // Vérifier la présence des lettres minuscules
        if (preg_match('/[a-z]/', $password)) {
            $has_lowercase = true;
        }
        // Vérifier la présence des lettres majuscules
        if (preg_match('/[A-Z]/', $password)) {
            $has_uppercase = true;
        }
        // Vérifier la présence des chiffres
        if (preg_match('/[0-9]/', $password)) {
            $has_digits = true;
        }
        // Vérifier la présence des caractères spéciaux
        if (preg_match($special_chars_pattern, $password)) {
            $has_special_chars = true;
        }

        // Vérifier la présence des séparateurs
        if (preg_match($separators_pattern, $password)) {
            $has_separators = true;
        }


        // Calculer la taille de l'ensemble de caractères utilisés
        if ($has_lowercase) {
            $charset_size += 26; // Lettres minuscules
        }
        if ($has_uppercase) {
            $charset_size += 26; // Lettres majuscules
        }
        if ($has_digits) {
            $charset_size += 10; // Chiffres
        }
        // Si des caractères spéciaux sont présents, ajouter leur taille au charset
        if ($has_special_chars) {
            $charset_size += 8; // Nombre de caractères spéciaux définis
        }

        // Si des séparateurs sont présents, ajouter leur taille au charset
        if ($has_separators) {
            $charset_size += 6; // Nombre de séparateurs définis
        }


        // Si aucun ensemble de caractères n'a été détecté, renvoyer 0
        if ($charset_size == 0) {
            return 0;
        }

        // Calcul de l'entropie : longueur du mot de passe multipliée par log2(taille du charset)
        $entropy = strlen($password) * log($charset_size, 2);

        return round($entropy, 2); // Retourner l'entropie arrondie à deux décimales
    }

    // Fonction pour renvoyer la classe Bootstrap en fonction de l'entropie
    public function getBootstrapEntropyClass(float $entropy): string
{
    if ($entropy < 80) {
        return 'very-weak'; // Très faible (gris foncé)
    } elseif ($entropy < 100) {
        return 'weak'; // Faible (rouge vif)
    } elseif ($entropy < 110) {
        return 'medium'; // Moyen (orange vif)
    } elseif ($entropy < 120) {
        return 'good'; // Bon (vert clair)
    } elseif ($entropy < 130) {
        return 'strong'; // Fort (bleu vif)
    } else {
        return 'very-strong'; // Très fort (violet)
    }
}

}
?>