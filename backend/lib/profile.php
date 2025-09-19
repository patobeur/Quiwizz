<?php

/**
 * Récupère les informations de profil d'un utilisateur.
 *
 * @param PDO $pdo
 * @param int $userId
 * @return array|null Les données du profil ou null si non trouvé.
 */
function get_user_profile(PDO $pdo, int $userId): ?array {
    $stmt = $pdo->prepare("SELECT id, email, pseudo, first_name, last_name, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();

    return $profile ?: null;
}

/**
 * Met à jour le profil d'un utilisateur.
 *
 * @param PDO $pdo
 * @param int $userId
 * @param array $data Les données à mettre à jour (pseudo, first_name, last_name)
 * @return array Résultat de l'opération.
 */
function update_user_profile(PDO $pdo, int $userId, array $data): array {
    // Validation
    $pseudo = $data['pseudo'] ?? null;
    $firstName = $data['first_name'] ?? null;
    $lastName = $data['last_name'] ?? null;

    if (empty($pseudo)) {
        return ['success' => false, 'message' => 'Le pseudo ne peut pas être vide.'];
    }
    if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
        return ['success' => false, 'message' => 'Le pseudo doit contenir entre 3 et 50 caractères.'];
    }

    // Vérifier si le nouveau pseudo est déjà pris par un autre utilisateur
    $stmt = $pdo->prepare("SELECT id FROM users WHERE pseudo = ? AND id != ?");
    $stmt->execute([$pseudo, $userId]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Ce pseudo est déjà utilisé.'];
    }

    // Mise à jour
    $stmt = $pdo->prepare(
        "UPDATE users SET pseudo = ?, first_name = ?, last_name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
    );

    if ($stmt->execute([$pseudo, $firstName, $lastName, $userId])) {
        // Mettre à jour le pseudo dans la session également
        $_SESSION['user_pseudo'] = $pseudo;
        return ['success' => true, 'message' => 'Profil mis à jour avec succès.'];
    }

    return ['success' => false, 'message' => 'Une erreur est survenue lors de la mise à jour.'];
}
