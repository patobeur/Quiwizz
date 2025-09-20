<?php

/**
 * Récupère tous les utilisateurs (pour les admins).
 *
 * @param PDO $pdo
 * @return array
 */
function get_all_users(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, email, pseudo, first_name, last_name, is_admin, created_at, updated_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Promeut un utilisateur au rang d'administrateur.
 *
 * @param PDO $pdo
 * @param int $userId
 * @return bool
 */
function promote_user_to_admin(PDO $pdo, int $userId): bool {
    $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
    return $stmt->execute([$userId]);
}

/**
 * Rétrograde un administrateur au rang d'utilisateur standard.
 *
 * @param PDO $pdo
 * @param int $userId
 * @return bool
 */
function demote_user_from_admin(PDO $pdo, int $userId): bool {
    $stmt = $pdo->prepare("UPDATE users SET is_admin = 0 WHERE id = ?");
    return $stmt->execute([$userId]);
}
