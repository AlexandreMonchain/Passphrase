# Testez moi
L'application [est disponible ici](https://passphrase.fr). Vous pouvez l'utiliser directement via ce lien ou cloner ce dépôt afin de réutiliser le code ou héberger votre propre instance de ce générateur.

# Générateur de Passphrase complexe en Français

Une application web développée avec Symfony pour générer des mots de passe sécurisés et personnalisables. Cet outil permet aux utilisateurs de définir le nombre de mots, les séparateurs, les caractères spéciaux et la longueur du mot de passe. Il offre également une estimation de l'entropie pour évaluer la sécurité des mots de passe générés.

## Fonctionnalités

- **Génération de mots de passe sécurisés** : Génération aléatoire de mots de passe robustes selon des critères définis par l'utilisateur.
- **Calcul de l'entropie** : L'algorithme calcule l'entropie du mot de passe (mesure de la force), avec un retour visuel basé sur des classes Bootstrap pour indiquer la robustesse du mot de passe (faible, moyen, fort).
- **API RESTful** : Une **API** est disponible pour permettre à d'autres applications de récupérer un ou plusieurs mots de passe via une requête HTTP. Les utilisateurs peuvent personnaliser la génération de mots de passe via les paramètres de l'API (nombre de mots de passe, longueur, caractères spéciaux, etc.).
[Testez l'API avec ce lien.](https://passphrase.fr/api/passwords?count=1&nb_mots=2&longueur_minimale=12&separateur=random&majuscule_debut=true&majuscule_aleatoire=false&longueur_nombre=2&caractere_special=random)
- **Cookie de sauvegarde des préférences** : Les préférences utilisateur (comme le nombre de mots, les séparateurs, etc.) sont sauvegardées dans un **cookie** afin de retrouver les mêmes paramètres lors de la prochaine utilisation de l'application.



## Mise en route

Clonez le dépôt, installez les vendors avec Composer et generez un .env


## Contribuer

Si vous rencontrez des problèmes ou avez des suggestions d'amélioration, n'hésitez pas à créer une issue ou à soumettre une pull request via le [dépôt GitHub](https://github.com/AlexandreMonchain/Passphrase).

## Licence

Ce projet est sous [licence MIT](./LICENSE).

---

Passphrase © 2024-2025 Alexandre MONCHAIN.
