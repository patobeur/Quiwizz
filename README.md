# Projet Web PHP - Phase 1 : Authentification & Profil

Ce projet est une application web simple développée en PHP vanilla, avec un frontend en HTML/CSS/JS pur. Cette première phase implémente un système d'authentification complet, la gestion de profil et un affichage placeholder pour des badges.

## Fonctionnalités

- **Backend en PHP 8.1+** sans framework.
- **Frontend découplé** en HTML5, CSS3 (flat design) et JavaScript (vanilla).
- **Base de données auto-initialisable** : supporte SQLite par défaut (aucune configuration requise) et MySQL.
- **API RESTful** pour toutes les interactions backend.
- **Système d'authentification complet** :
    - Inscription
    - Connexion / Déconnexion
    - Réinitialisation de mot de passe (avec envoi d'email simulé)
- **Gestion de profil utilisateur** (consultation et mise à jour).
- **Affichage de badges** (données statiques pour la Phase 1).
- **Sécurité** :
    - Mots de passe hachés (Argon2/Bcrypt).
    - Protection CSRF sur les actions sensibles.
    - Sessions sécurisées (HttpOnly, SameSite).
    - En-têtes de sécurité (CSP, X-Frame-Options, etc.).
    - Limitation de débit basique sur les actions sensibles.

## Installation

1.  **Cloner le dépôt**
    ```bash
    git clone <url-du-repo>
    cd <nom-du-repo>
    ```

2.  **Configurer un serveur local**
    Le plus simple est d'utiliser le serveur web intégré de PHP. Placez-vous à la racine du projet et lancez :
    ```bash
    php -S localhost:8000 -t frontend
    ```
    Ceci démarre un serveur où le dossier `frontend` est la racine publique (docroot). L'application sera accessible à `http://localhost:8000`. Le dossier `backend` ne sera pas accessible directement, comme il se doit.

3.  **Configurer l'environnement**
    Le backend a besoin d'un fichier de configuration `env.php`.
    ```bash
    cp backend/env.example.php backend/env.php
    ```
    Ouvrez `backend/env.php` et ajustez les paramètres.

## Configuration

### Base de données (SQLite)

Par défaut, l'application utilise SQLite. **Aucune configuration n'est requise.** La base de données sera automatiquement créée dans `backend/storage/app.db` et les tables seront créées au premier lancement.

### Base de données (MySQL)

Pour utiliser MySQL :
1.  Créez une base de données sur votre serveur MySQL.
2.  Modifiez le fichier `backend/env.php` avec les informations suivantes :
    ```php
    'DB_DRIVER' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_NAME' => 'votre_nom_de_db',
    'DB_USER' => 'votre_utilisateur',
    'DB_PASS' => 'votre_mot_de_passe',
    ```
    Au premier lancement, les tables seront automatiquement créées dans la base de données spécifiée.

### Envoi d'e-mails (simulé)

Pour la réinitialisation de mot de passe, les e-mails ne sont pas réellement envoyés. Ils sont écrits dans un fichier de log pour faciliter le développement. Vous trouverez les "e-mails envoyés" et les liens de réinitialisation dans :
`backend/storage/logs/sent_emails.log`

Pour une configuration réelle (avec PHPMailer par exemple), vous devriez remplir les informations SMTP dans `backend/env.php`.

## Checklist de Tests Manuels

- [ ] **Inscription** :
    - [ ] Créer un nouveau compte avec succès.
    - [ ] Tenter de créer un compte avec un email ou pseudo déjà utilisé (doit échouer).
    - [ ] Tenter de s'inscrire avec un mot de passe trop court (doit échouer).
- [ ] **Connexion / Déconnexion** :
    - [ ] Se connecter avec des identifiants valides.
    - [ ] Tenter de se connecter avec un mot de passe incorrect (doit échouer).
    - [ ] Une fois connecté, vérifier que la barre de navigation change (affiche "Profil", "Déconnexion").
    - [ ] Se déconnecter (doit rediriger vers l'accueil et changer la barre de navigation).
- [ ] **Profil** :
    - [ ] Accéder à la page de profil (doit afficher les informations de l'utilisateur).
    - [ ] Modifier les informations du profil (pseudo, prénom, nom) et enregistrer.
    - [ ] Vérifier que les informations ont bien été mises à jour.
- [ ] **Badges** :
    - [ ] Accéder à la page des badges (doit afficher la liste des badges statiques).
- [ ] **Réinitialisation de mot de passe** :
    - [ ] Demander une réinitialisation pour un email existant.
    - [ ] Vérifier le fichier `backend/storage/logs/sent_emails.log` pour trouver le lien/token.
    - [ ] Accéder au lien de réinitialisation (`/#reset=TOKEN`).
    - [ ] Entrer un nouveau mot de passe et valider.
    - [ ] Tenter de se connecter avec le nouveau mot de passe.
- [ ] **Sécurité** :
    - [ ] Tenter d'accéder à la page de profil sans être connecté (doit rediriger vers la connexion).
    - [ ] (Difficile à tester manuellement) Tenter de faire une action POST (ex: logout) sans token CSRF (devrait échouer).
    - [ ] Tenter de se connecter plusieurs fois avec un mauvais mot de passe rapidement (doit afficher une erreur "Trop de tentatives").
