<?php
// Gestion de la session : savoir qui est connecté et protéger les pages

// Démarre la session une seule fois
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,    // le cookie n'est pas lisible en JavaScript
        'samesite' => 'Strict' // le cookie n'est pas envoyé depuis un autre site
    ]);
    session_start();
}

// Vrai si un utilisateur est connecté
function est_connecte()
{
    return isset($_SESSION['id_utilisateur']);
}

// Vrai si l'utilisateur connecté est administrateur
function est_admin()
{
    return est_connecte() && $_SESSION['role'] === 'admin';
}

// À mettre en haut des pages réservées aux utilisateurs connectés
function exiger_connexion()
{
    if (!est_connecte()) {
        header('Location: connexion.php');
        exit;
    }
}

// À mettre en haut des pages réservées à l'administrateur
function exiger_admin()
{
    exiger_connexion();
    if (!est_admin()) {
        http_response_code(403);
        exit('Accès réservé à l\'administrateur.');
    }
}