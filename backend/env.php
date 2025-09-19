<?php

// Fichier de configuration d'exemple.
// Copier ce fichier en `env.php` et ajuster les valeurs.
// Le fichier `env.php` est ignoré par Git pour des raisons de sécurité.

return [
    // -- Configuration de la base de données --
    // Driver: 'sqlite' ou 'mysql'
    'DB_DRIVER' => 'sqlite',

    // Pour SQLite
    'DB_PATH' => __DIR__ . '/storage/app.db',

    // Pour MySQL
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_NAME' => 'quiwizz',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_CHARSET' => 'utf8mb4',

    // -- Configuration de l'envoi d'e-mails (PHPMailer) --
    'MAIL_HOST' => 'smtp.example.com',
    'MAIL_PORT' => 587,
    'MAIL_USERNAME' => 'patobeur@patobeur.fr',
    'MAIL_PASSWORD' => 'secret',
    'MAIL_FROM_ADDRESS' => 'noreply@patobeur.fr',
    'MAIL_FROM_NAME' => 'QuiWizz',
    'MAIL_ENCRYPTION' => 'tls', // 'tls' ou 'ssl'

    // -- Paramètres de l'application --
    'APP_URL' => 'http://localhost:8000/quiwizz/backend/index.pxp', // URL publique de l'application
    'DEBUG' => true, // Activer/désactiver les messages d'erreur détaillés
];
