<?php

use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase {
    private $pdo;

    protected function setUp(): void {
        // Utiliser une base de données SQLite en mémoire pour les tests
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table des utilisateurs
        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL UNIQUE,
                pseudo TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                first_name TEXT,
                last_name TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME,
                reset_token TEXT,
                reset_expires_at DATETIME
            )
        ");
    }

    protected function tearDown(): void {
        // La base de données en mémoire est automatiquement détruite
        $this->pdo = null;
    }

    public function testUpdateUserProfileOnlyPseudo() {
        // 1. Insérer un utilisateur de test avec des données complètes
        $userId = 1;
        $email = 'test@example.com';
        $pseudo = 'testuser';
        $passwordHash = password_hash('password', PASSWORD_DEFAULT);
        $firstName = 'John';
        $lastName = 'Doe';

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (id, email, pseudo, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $email, $pseudo, $passwordHash, $firstName, $lastName]);

        // 2. Appeler la fonction de mise à jour du profil en ne passant que le pseudo
        $newPseudo = 'newpseudo';
        $data = ['pseudo' => $newPseudo];

        // Simuler la session
        $_SESSION['user_pseudo'] = $pseudo;

        $result = update_user_profile($this->pdo, $userId, $data);

        // Vérifier que la mise à jour a réussi
        $this->assertTrue($result['success']);
        $this->assertEquals('Profil mis à jour avec succès.', $result['message']);
        $this->assertEquals($newPseudo, $_SESSION['user_pseudo']);

        // 3. Récupérer le profil mis à jour depuis la base de données
        $profile = get_user_profile($this->pdo, $userId);

        // 4. Vérifier que le pseudo a été mis à jour
        $this->assertEquals($newPseudo, $profile['pseudo']);

        // 5. Vérifier que les autres champs n'ont pas été effacés (C'est ici que le bug se manifeste)
        $this->assertEquals($firstName, $profile['first_name'], "Le prénom ne devrait pas avoir été effacé.");
        $this->assertEquals($lastName, $profile['last_name'], "Le nom de famille ne devrait pas avoir été effacé.");
    }
}
