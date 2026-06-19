[![Version 1.3.2](https://img.shields.io/badge/Version-1.3.2-blue)](https://github.com/AlexandreMonchain/Passphrase/releases)
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

- [Voir l'extension sur le Chrome Web Store](https://chromewebstore.google.com/detail/g%C3%A9n%C3%A9rateur-de-passphrase/bhlgfoknmmhgpfoanhoemjccephcanjd)
- [Code source de l'extension sur GitHub](https://github.com/AlexandreMonchain/PassphraseChromeExtension)

## Installation locale

### Avec Symfony CLI (développement)

```bash
# 1. Cloner le dépôt
git clone https://github.com/AlexandreMonchain/Passphrase.git
cd Passphrase

# 2. Installer les dépendances
composer install

# 3. Créer le fichier .env
cp .env.example .env

# 4. Lancer le serveur
symfony server:start --no-tls

# 5. Ouvrir http://127.0.0.1:8000
```

**Prérequis**: PHP 8.2+, Composer 2.0+

### Avec Docker (production-ready)

```bash
# Démarrer le conteneur (clone + install + run automatique)
APP_SECRET=votre_secret_ici docker compose up -d --build

# Ouvrir http://localhost:8000
```

**Prérequis**: Docker 20.10+, Docker Compose v2

**Personnalisation (optionnel):**
```bash
# Utiliser la branche dev
GIT_BRANCH=dev APP_SECRET=votre_secret_ici docker compose up -d --build

# Utiliser un fork perso
GIT_REPO=https://github.com/votreusername/Passphrase.git APP_SECRET=votre_secret_ici docker compose up -d --build

# Changer le port d'écoute (défaut: 8000)
PORT=9000 APP_SECRET=votre_secret_ici docker compose up -d --build
```

**Commandes utiles:**
```bash
# Voir les logs
docker compose logs -f web

# Accéder au container
docker compose exec web sh

# Arrêter
docker compose down

# Rebuild complet (nouvelle version)
docker compose up -d --build
```

**Qu'est-ce qui se passe au démarrage:**
1. 🔨 Build de l'image (télécharge PHP 8.4, Apache)
2. 📥 Clone depuis GitHub (branche `main` par défaut)
3. 📦 Installation des dépendances Composer
4. 🧹 Warmup du cache Symfony
5. ✅ Démarrage d'Apache

## API REST

**Endpoint**: `GET /api/passwords`

### Exemple rapide

```bash
curl "https://passphrase.fr/api/passwords?count=3&nb_mots=2&longueur_minimale=15"
```

Réponse:
```json
{
  "passwords": [
    "Economie-Etrier57:",
    "Menton-Pancarte65!",
    "Sieger-Distribuer52#"
  ]
}
```

### Paramètres

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `count` | int | 1 | Nombre de passphrases (1-100) |
| `nb_mots` | int | 2 | Mots par passphrase (2-7) |
| `longueur_minimale` | int | 12 | Longueur min (8-50) |
| `separateur` | string | random | Séparateur (-, _, *, random) |
| `majuscule_debut` | bool | true | Majuscule au début |
| `majuscule_aleatoire` | bool | false | Majuscules aléatoires |
| `longueur_nombre` | int | 2 | Longueur des chiffres (0-5) |
| `caractere_special` | string | random | Type spéciaux (random, none, !@#) |
| `caracteres_accentues` | bool | true | Inclure accents |

### Intégration JavaScript

```javascript
fetch("https://passphrase.fr/api/passwords?count=5&nb_mots=3")
  .then(res => res.json())
  .then(data => console.log(data.passwords));
```

**CORS activé** ✅ — Utilisez l'API depuis vos applications web!

## Contribution

Si vous rencontrez des problèmes ou avez des suggestions d'amélioration, n'hésitez pas à créer une issue ou à soumettre une pull request via le [dépôt GitHub](https://github.com/AlexandreMonchain/Passphrase).

## Architecture

- **Framework**: Symfony 7.4 LTS
- **Runtime**: PHP 8.2+ (8.4 dans Docker)
- **Frontend**: Twig + Bootstrap 5 + Vanilla JS + Custom CSS Design System
- **Storage**: Fichier CSV (dictionnaire de mots français)
- **Container**: Docker + Apache

### Structure du projet

```
├── src/
│   ├── Controller/          # Routes et API
│   ├── Service/             # Logique métier
│   └── Form/                # Formulaires Symfony
├── templates/               # Pages Twig
├── public/assets/           # CSS/JS
├── config/                  # Configuration Symfony
├── docker/                  # Configuration Docker (Apache, entrypoint)
├── Dockerfile               # Image PHP 8.4 + Apache
├── docker-compose.yml       # Service Web
└── tests/                   # Tests PHPUnit
```

## Performance & Sécurité

- ✅ Cache HTTP headers
- ✅ CSP (Content Security Policy) headers
- ✅ CSRF protection
- ✅ XSS protection (Twig auto-escaping)

## Déploiement en production

### Avec Docker (recommandé)

```bash
APP_SECRET=votre_secret_ici docker compose up -d --build
# App dispo sur http://ip_docker:8000
```

Le conteneur écoute en HTTP interne (port 80, publié sur 8000).

Pour la prod, faites au choix:

1. Mettre l'app derrière un reverse proxy (Nginx/Caddy/Traefik) qui gère HTTPS en 443.
2. Modifier la conf pour servir directement en 443 avec un certificat TLS.

Ne JAMAIS exposer une application en HTTP en production.

## Licence

[MIT License](LICENSE)

---

**Passphrase v1.3.2** | **© 2024-2026 Alexandre MONCHAIN** | [GitHub](https://github.com/AlexandreMonchain/Passphrase) | [Chrome Extension](https://chromewebstore.google.com/detail/générateur-de-passphrase/bhlgfoknmmhgpfoanhoemjccephcanjd)
