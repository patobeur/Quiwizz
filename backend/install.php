<?php

// Ce script ne doit être appelé que par index.php
// Il ne doit pas être accessible directement.
if (!defined('QUIWIZZ_INSTALL')) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès interdit']);
    exit;
}

// --- Fonctions d'aide ---

/**
 * Envoie une réponse d'erreur JSON et termine le script.
 * @param string $message Le message d'erreur.
 * @param int $code Le code de statut HTTP.
 */
function send_install_error($message, $code = 400) {
    http_response_code($code);
    // Assurer que le fichier env.php est supprimé en cas d'erreur
    if (file_exists(__DIR__ . '/env.php')) {
        unlink(__DIR__ . '/env.php');
    }
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

/**
 * Génère le contenu du fichier env.php.
 * @param array $data Les données de configuration.
 * @return string Le contenu du fichier PHP.
 */
function generate_env_content(array $data): string {
    $env_content = "<?php\n\n";
    $env_content .= "// Fichier de configuration généré par l'installateur de QuiWizz.\n\n";
    $env_content .= "return [\n";

    $bool_to_string = function($val) { return $val ? 'true' : 'false'; };

    foreach ($data as $key => $value) {
        if (in_array($key, ['admin_email', 'admin_pseudo', 'admin_password'])) continue;

        $env_content .= "    // -- Valeur pour $key --\n";
        if (is_bool($value)) {
            $env_content .= "    '$key' => " . $bool_to_string($value) . ",\n\n";
        } else if (is_numeric($value) && !in_array($key, ['DB_PORT'])) {
             $env_content .= "    '$key' => $value,\n\n";
        } else {
             $env_content .= "    '$key' => '" . addslashes($value) . "',\n\n";
        }
    }
    $env_content .= "];\n";

    return $env_content;
}

/**
 * Crée les tables de la base de données sans ajouter d'utilisateur par défaut.
 * @param PDO $pdo L'objet de connexion PDO.
 */
function create_tables_for_install(PDO $pdo) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    $users_sql = "
    CREATE TABLE IF NOT EXISTS users (
        id " . ($driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY') . ",
        email VARCHAR(255) NOT NULL UNIQUE,
        pseudo VARCHAR(50) NOT NULL UNIQUE,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        password_hash VARCHAR(255) NOT NULL,
        total_points INT DEFAULT 0,
        is_admin INTEGER NOT NULL DEFAULT 0,
        reset_token VARCHAR(255),
        reset_expires_at DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );";

    $badges_sql = "
    CREATE TABLE IF NOT EXISTS badges (
        id " . ($driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY') . ",
        slug VARCHAR(50) NOT NULL UNIQUE,
        label VARCHAR(100) NOT NULL,
        color VARCHAR(7) NOT NULL,
        description TEXT
    );";

    $user_badges_sql = "
    CREATE TABLE IF NOT EXISTS user_badges (
        user_id INT NOT NULL,
        badge_id INT NOT NULL,
        level INT DEFAULT 1,
        awarded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, badge_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
    );";

    $sondages_sql = "
    CREATE TABLE IF NOT EXISTS sondages (
        id " . ($driver === 'sqlite' ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY') . ",
        title VARCHAR(255) NOT NULL,
        description TEXT,
        status VARCHAR(10) NOT NULL DEFAULT 'draft',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );";

    $pdo->exec($users_sql);
    $pdo->exec($badges_sql);
    $pdo->exec($user_badges_sql);
    $pdo->exec($sondages_sql);
}


// --- Processus d'Installation ---

// 1. Vérifier que env.php n'existe pas déjà
if (file_exists(__DIR__ . '/env.php')) {
    send_install_error('L\'application semble déjà être installée (env.php existe).', 409);
}

// 2. Récupérer et valider les données POST
// Les données sont déjà décodées par index.php et se trouvent dans $_POST
$input = $_POST;
if (empty($input)) {
    send_install_error('Données d\'installation invalides ou manquantes.');
}

// Validation simple
$required_fields = ['admin_email', 'admin_pseudo', 'admin_password', 'DB_DRIVER', 'APP_URL'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        send_install_error("Le champ '$field' est requis.");
    }
}
if (!filter_var($input['admin_email'], FILTER_VALIDATE_EMAIL)) {
    send_install_error("Format d'email administrateur invalide.");
}
if (strlen($input['admin_password']) < 8) {
    send_install_error("Le mot de passe administrateur doit contenir au moins 8 caractères.");
}


// 3. Générer et écrire le fichier env.php
$config_data = $input;
$config_data['DEBUG'] = isset($input['DEBUG']) ? (bool)$input['DEBUG'] : false;
$env_content = generate_env_content($config_data);

if (file_put_contents(__DIR__ . '/env.php', $env_content) === false) {
    send_install_error('Impossible d\'écrire le fichier de configuration env.php. Vérifiez les permissions.', 500);
}


// 4. Tenter la connexion à la BDD, créer les tables et l'admin
try {
    // Charger le bootstrap pour avoir accès aux fonctions
    require_once __DIR__ . '/bootstrap.php';
    global $pdo, $config; // $pdo et $config sont initialisés dans bootstrap

    // Créer les tables
    create_tables_for_install($pdo);

    // Enregistrer le nouvel utilisateur admin
    $admin_email = sanitize_input($input['admin_email']);
    $admin_pseudo = sanitize_input($input['admin_pseudo']);
    $admin_password = $input['admin_password'];

    $registration_result = register_user($pdo, $admin_email, $admin_pseudo, $admin_password);

    if (!$registration_result['success']) {
        throw new Exception("Échec de la création de l'utilisateur admin : " . $registration_result['message']);
    }

    // Récupérer l'ID de l'utilisateur nouvellement créé
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception("Impossible de retrouver l'utilisateur admin après sa création.");
    }
    $admin_id = $user['id'];

    // Promouvoir l'utilisateur en admin
    if (!promote_user_to_admin($pdo, $admin_id)) {
        throw new Exception("Impossible de promouvoir l'utilisateur en administrateur.");
    }

    // 5. Succès !
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Installation terminée avec succès !'
    ]);

} catch (Throwable $e) {
    // En cas d'erreur, supprimer env.php et renvoyer l'erreur.
    send_install_error('Erreur lors de l\'installation : ' . $e->getMessage(), 500);
}
