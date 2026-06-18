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

- [Voir l’extension sur le Chrome Web Store](https://chromewebstore.google.com/detail/g%C3%A9n%C3%A9rateur-de-passphrase/bhlgfoknmmhgpfoanhoemjccephcanjd)
- [Code source de l’extension sur GitHub](https://github.com/AlexandreMonchain/PassphraseChromeExtension)

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
docker-compose up -d --build

# Ouvrir http://localhost:8000
```

**Prérequis**: Docker 20.10+, Docker Compose 1.29+

**Personnalisation (optionnel):**
```bash
# Utiliser une autre branche
GIT_BRANCH=develop docker-compose up -d --build

# Utiliser un fork perso
GIT_REPO=https://github.com/votreusername/Passphrase.git docker-compose up -d --build
```

**Commandes utiles:**
```bash
# Voir les logs
docker-compose logs -f web

# Accéder au container
docker-compose exec web sh

# Arrêter
docker-compose down

# Arrêter et supprimer les volumes (réinitialiser)
docker-compose down -v
```

**Qu'est-ce qui se passe au démarrage:**
1. 🔨 Build de l'image (télécharge PHP 8.2, Nginx, Supervisor)
2. 📥 Clone depuis GitHub (repo propre, pas de fichiers locaux)
3. 📦 Installation des dépendances Composer
4. 🧹 Warmup du cache Symfony
5. ✅ Démarrage de Nginx + PHP-FPM
6. 💾 Les données de cache/logs sont persistées dans des volumes Docker

## Tests

```bash
php bin/phpunit
```

Ou avec Docker:
```bash
docker-compose exec web php bin/phpunit
```

## API REST

**Endpoint**: `GET /api/passwords`

**Documentation complète**: [/api/documentation](/api/documentation)

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
- **Runtime**: PHP 8.2+
- **Frontend**: Twig + Bootstrap 5 + Stimulus.js + Custom CSS Design System
- **Storage**: CSV file cache (1h refresh)
- **Container**: Docker + Docker Compose (Nginx + PHP-FPM)
- **Design**: Responsive, Custom typography

### Structure du projet

```
├── src/
│   ├── Controller/          # Routes et API
│   ├── Service/             # Logique métier
│   ├── Entity/              # Modèles
│   └── Form/                # Formulaires Symfony
├── templates/               # Pages Twig
├── public/assets/           # CSS/JS compilés
├── config/                  # Configuration Symfony
├── migrations/              # Migrations BD
├── docker/                  # Configuration Docker
├── Dockerfile              # Image PHP-FPM
├── docker-compose.yml      # Services (Web + DB)
└── tests/                   # Tests PHPUnit
```

## Performance & Sécurité

- ✅ HTTP/2 support
- ✅ Compression Gzip
- ✅ Cache HTTP headers
- ✅ CSP (Content Security Policy) headers
- ✅ CSRF protection
- ✅ XSS protection (Twig auto-escaping)

## Bonnes Pratiques en Production

⚠️ **Le conteneur Docker expose le port 80 en HTTP non chiffré.** En production, vous DEVEZ positionner Passphrase derrière un **reverse proxy** qui gère:

- **HTTPS/TLS** — Chiffrement des données en transit (certificat SSL/Let's Encrypt)
- **Rate limiting** — Limiter les requêtes pour éviter les abus
- **Security headers** — Headers HTTP de sécurité (HSTS, X-Frame-Options, etc.)
- **Load balancing** — Répartition de charge si plusieurs instances
- **DDoS protection** — Filtrage des attaques distribuées

**Solutions recommandées:**
- **Nginx** (reverse proxy local) — Simple, performant
- **Caddy** — Auto-HTTPS avec Let's Encrypt
- **Traefik** — Orchestration multi-conteneurs
....

**Exemple avec Nginx (voir section Déploiement)** — Le reverse proxy écoute en HTTPS (443) et forwarde vers le conteneur sur HTTP (8000).

## Déploiement en production

### Avec Docker (recommandé)

**Architecture recommandée:**
```
Internet (HTTPS 443)
    ↓
Caddy / Nginx (reverse proxy, TLS)
    ↓ (HTTP interne, port 80)
Passphrase container
```

**Option 1: Reverse proxy externe (Nginx local)**

Si vous avez déjà un Nginx/Caddy sur votre serveur:

1. Démarrer Passphrase en exposant le port 8000:
```bash
docker-compose up -d --build
# → Écoute sur http://localhost:8000
```

2. Configurer votre reverse proxy pour écouter en HTTPS et renvoyer vers le container:

```nginx
server {
    listen 443 ssl http2;
    server_name passphrase.fr;
    
    ssl_certificate /etc/letsencrypt/live/passphrase.fr/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/passphrase.fr/privkey.pem;

    location / {
        # Le container écoute sur le port 80 INTERNE (8000 du HOST)
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_buffering off;
    }
}

# Redirection HTTP → HTTPS
server {
    listen 80;
    server_name passphrase.fr;
    return 301 https://$server_name$request_uri;
}
```

**Option 2: Caddy en Docker (tout-en-un)**

Plus simple: un container Caddy gère HTTPS et renvoie vers Passphrase en réseau interne.

Créer un `docker-compose.prod.yml`:

```yaml
version: '3.9'

services:
  caddy:
    image: caddy:2-alpine
    container_name: passphrase-caddy
    ports:
      - "80:80"
      - "443:443"
    environment:
      - DOMAIN=passphrase.fr
    volumes:
      - ./docker/Caddyfile:/etc/caddy/Caddyfile:ro
      - caddy_data:/data
      - caddy_config:/config
    networks:
      - passphrase
    restart: unless-stopped

  web:
    build:
      context: .
      dockerfile: Dockerfile
      args:
        GIT_REPO: ${GIT_REPO:-https://github.com/AlexandreMonchain/Passphrase.git}
        GIT_BRANCH: ${GIT_BRANCH:-main}
    container_name: passphrase-web
    environment:
      APP_ENV: prod
      APP_DEBUG: 0
    volumes:
      - app_cache:/app/var/cache
      - app_logs:/app/var/log
    networks:
      - passphrase
    restart: unless-stopped

volumes:
  app_cache:
  app_logs:
  caddy_data:
  caddy_config:

networks:
  passphrase:
    driver: bridge
```

Créer `docker/Caddyfile`:

```caddyfile
{$DOMAIN:localhost} {
    reverse_proxy web:80 {
        header_uri X-Forwarded-Proto https
    }
}
```

Lancer:
```bash
DOMAIN=passphrase.fr docker-compose -f docker-compose.prod.yml up -d --build
# → Caddy écoute sur 80/443 (HTTPS automatique)
# → Renvoie en interne vers web:80
```

### Variables d'environnement (optionnel)

```bash
# Production
export APP_ENV=prod
export APP_DEBUG=0

# Utiliser un fork
export GIT_REPO=https://github.com/votreusername/Passphrase.git
export GIT_BRANCH=main
```

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Licence

[MIT License](LICENSE)

---

**Passphrase v1.3.2** | **© 2024-2026 Alexandre MONCHAIN** | [GitHub](https://github.com/AlexandreMonchain/Passphrase) | [Chrome Extension](https://chromewebstore.google.com/detail/générateur-de-passphrase/bhlgfoknmmhgpfoanhoemjccephcanjd)
