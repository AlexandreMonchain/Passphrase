[![Symfony 7.4 LTS](https://img.shields.io/badge/Symfony-7.4%20LTS-000?logo=symfony)](https://symfony.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777?logo=php)](https://php.net)
[![License MIT](https://img.shields.io/badge/License-MIT-green)](LICENSE)

**🚀 [Passphrase.fr](https://passphrase.fr)** — Générateur de mots de passe sécurisés en français.  
**🐙 [Code source](https://github.com/AlexandreMonchain/Passphrase) sur GitHub**

# Générateur de Passphrases complexes en Français

Une application web développée avec Symfony pour générer des mots de passe sécurisés et personnalisables. Cet outil permet aux utilisateurs de définir le nombre de mots, les séparateurs, les caractères spéciaux et la longueur du mot de passe. Il offre également une estimation de l'entropie pour évaluer la sécurité des mots de passe générés.

## Fonctionnalités

- **Génération de mots de passe sécurisés** : Génération aléatoire de mots de passe robustes selon des critères définis par l'utilisateur.
- **Calcul de l'entropie** : L'algorithme calcule l'entropie du mot de passe (mesure de la force), avec un retour visuel basé sur des classes Bootstrap pour indiquer la robustesse du mot de passe (faible, moyen, fort).
- **API RESTful** : Une **API** est disponible pour permettre à d'autres applications de récupérer un ou plusieurs mots de passe via une requête HTTP. Les utilisateurs peuvent personnaliser la génération de mots de passe via les paramètres de l'API (nombre de mots de passe, longueur, caractères spéciaux, etc.).
[Testez l'API avec ce lien.](https://passphrase.fr/api/passwords?count=1&nb_mots=2&longueur_minimale=12&separateur=random&majuscule_debut=true&majuscule_aleatoire=false&longueur_nombre=2&caractere_special=random&caracteres_accentues=false)
- **Cookie de sauvegarde des préférences** : Les préférences utilisateur (comme le nombre de mots, les séparateurs, etc.) sont sauvegardées dans un **cookie** afin de retrouver les mêmes paramètres lors de la prochaine utilisation de l'application.

## Extension Chrome

Une extension Chrome est disponible pour accéder rapidement au générateur de mots de passe directement depuis votre navigateur.

- [Voir l’extension sur le Chrome Web Store](https://chromewebstore.google.com/detail/g%C3%A9n%C3%A9rateur-de-passphrase/bhlgfoknmmhgpfoanhoemjccephcanjd)
- [Code source de l’extension sur GitHub](https://github.com/AlexandreMonchain/PassphraseChromeExtension)

## Installation locale

```bash
# 1. Cloner le dépôt
git clone https://github.com/AlexandreMonchain/Passphrase.git
cd Passphrase

# 2. Installer les dépendances
composer install

# 3. Créer le fichier .env
cp .env.example .env  # ou créer manuellement

# 4. Lancer le serveur
symfony server:start --no-tls

# 5. Ouvrir http://127.0.0.1:8000
```

**Prérequis**: PHP 8.2+, Composer 2.0+

## Tests

```bash
php bin/phpunit
```

## Contribution

Si vous rencontrez des problèmes ou avez des suggestions d'amélioration, n'hésitez pas à créer une issue ou à soumettre une pull request via le [dépôt GitHub](https://github.com/AlexandreMonchain/Passphrase).

## API REST

**Endpoint**: `GET /api/passwords`

Exemple:
```bash
curl "https://passphrase.fr/api/passwords?count=3&nb_mots=2&longueur_minimale=15"
```

Paramètres: `nb_mots` (2-7), `count` (1-100), `longueur_minimale` (8-50), `separateur`, `majuscule_debut`, `majuscule_aleatoire`, `longueur_nombre`, `caractere_special`, `caracteres_accentues`

## Architecture

- **Framework**: Symfony 7.4 LTS
- **Backend**: PHP 8.2+ avec service layer
- **Frontend**: Twig + Bootstrap 5 + Stimulus.js
- **Cache**: Symfony Cache (CSV 1h)
- **Storage**: SQLite (dev), configurable

## Licence

[MIT License](LICENSE)

---

**Passphrase © 2024-2025 Alexandre MONCHAIN** | [GitHub](https://github.com/AlexandreMonchain/Passphrase) | [Chrome Extension](https://chromewebstore.google.com/detail/générateur-de-passphrase/bhlgfoknmmhgpfoanhoemjccephcanjd)
