<?php
require '/var/www/config.php';

// Connexion à la base de données
function connexionBdd() {
    return new PDO(
        'mysql:host=' . DB_HOTE . ';dbname=' . DB_NOM . ';charset=utf8mb4',
        DB_UTILISATEUR,
        DB_MOT_DE_PASSE,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}

// Envoie une réponse JSON avec le bon code HTTP, puis arrête le script
function repondre($code, $donnees) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($donnees);
    exit;
}

// Vérifie la clé API envoyée dans l'en-tête X-API-KEY
function verifierCleApi() {
    $cle = $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (!hash_equals(API_CLE, $cle)) {
        repondre(401, ['erreur' => 'Clé API manquante ou invalide']);
    }
}
