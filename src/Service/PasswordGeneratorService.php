<?php
// src/Service/PasswordGeneratorService.php
namespace App\Service;

class PasswordGeneratorService
{
    private array $caracteresSpeciaux = ['$', '*', '!', ':', ';', ',', '?', '#'];
    private array $separateurs = ['-', '_', ' ', '*', '/', '+'];

    public function generatePasswords(array $data, array $mots, int $count = 10, bool $withEntropy = true): array
    {
        $passwordsWithEntropy = [];

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

                    if (empty($data['caracteres_accentues'])) {
                        $mot = $this->removeAccents($mot);
                    }

                    $passwordParts[] = $mot;
                }

                $separateur = $data['separateur'] === 'random'
                    ? $this->separateurs[array_rand($this->separateurs)]
                    : $data['separateur'];

                $password = implode($separateur, $passwordParts);

                if ($data['longueur_nombre'] > 0) {
                    $nombreAleatoire = str_pad(random_int(0, (10 ** $data['longueur_nombre']) - 1), $data['longueur_nombre'], '0', STR_PAD_LEFT);
                    $password .= $nombreAleatoire;
                }

                if ($data['caractere_special'] === 'random') {
                    $password .= $this->caracteresSpeciaux[array_rand($this->caracteresSpeciaux)];
                } elseif ($data['caractere_special'] !== 'none') {
                    $password .= $data['caractere_special'];
                }

            } while (strlen($password) < $data['longueur_minimale']);

            if ($withEntropy) {
                // Calcul de l'entropie du mot de passe
                $entropy = $this->calculateEntropy($password, !empty($data['caracteres_accentues']));

                // Ajouter le mot de passe et son entropie dans le tableau
                $passwordsWithEntropy[] = [
                    'password' => $password,
                    'entropy' => $entropy,
                    'class' => $this->getBootstrapEntropyClass($entropy)
                ];
            } else {
                // Si l'entropie n'est pas nécessaire, ajouter simplement le mot de passe
                $passwordsWithEntropy[] = [
                    'password' => $password
                ];
            }
        }

        return $passwordsWithEntropy;
    }

    private function calculateEntropy(string $password, bool $accents = true): float
{
    $charset_size = 0;

    // Vérification des types de caractères présents dans le mot de passe
    $has_lowercase = preg_match('/[a-z]/', $password);
    $has_uppercase = preg_match('/[A-Z]/', $password);
    $has_digits = preg_match('/[0-9]/', $password);
    $has_special_chars = preg_match('/[\$\*\!\:\;\,\?\#]/', $password);
    $has_separators = preg_match('/[\-_ \*\/\+]/', $password);
    $has_accents = $accents ? preg_match('/[éèùàçâêëîïô]/i', $password) : 0;

    // Calcul de la taille de l'ensemble de caractères
    $charset_size += $has_lowercase ? 26 : 0;
    $charset_size += $has_uppercase ? 26 : 0;
    $charset_size += $has_digits ? 10 : 0;
    $charset_size += $has_special_chars ? 8 : 0;
    $charset_size += $has_separators ? 6 : 0;
    $charset_size += $has_accents ? 11 : 0;

    if ($charset_size == 0) {
        return 0;
    }

    // Utilisation de mb_strlen() pour obtenir la longueur correcte en caractères
    $length = mb_strlen($password, 'UTF-8');
    $entropy = $length * log($charset_size, 2);

    return round($entropy, 0);
}
    

    public function getBootstrapEntropyClass(float $entropy): string
    {
        if ($entropy < 80) {
            return 'very-weak';
        } elseif ($entropy < 100) {
            return 'weak';
        } elseif ($entropy < 110) {
            return 'medium';
        } elseif ($entropy < 120) {
            return 'good';
        } elseif ($entropy < 130) {
            return 'strong';
        } else {
            return 'very-strong';
        }
    }

    private function removeAccents(string $str): string
    {
        // 1. Translittération classique
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);

        // 2. Suppression des caractères non désirés générés par iconv (apostrophe, backtick, etc.)
        $str = preg_replace('/[^A-Za-z0-9 _\-*\/+]/', '', $str);

        return $str;
    }
}

?>