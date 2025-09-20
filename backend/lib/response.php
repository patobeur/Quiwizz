<?php

/**
 * Envoie une réponse JSON au client.
 *
 * @param mixed $data Les données à encoder en JSON.
 * @param int $statusCode Le code de statut HTTP à envoyer (par défaut 200).
 */
function send_json_response($data, $statusCode = 200) {
    // Nettoyer tout output qui aurait pu être généré avant (erreurs, espaces etc.)
    if (ob_get_level() > 0) {
        ob_clean();
    }

    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Envoie une réponse d'erreur JSON standardisée.
 *
 * @param string $message Le message d'erreur.
 * @param int $statusCode Le code de statut HTTP (par défaut 400).
 * @param array|null $details Des détails supplémentaires sur l'erreur.
 */
function send_error_response($message, $statusCode = 400, $details = null) {
    $response = ['error' => $message];
    if ($details !== null) {
        $response['details'] = $details;
    }
    send_json_response($response, $statusCode);
}
