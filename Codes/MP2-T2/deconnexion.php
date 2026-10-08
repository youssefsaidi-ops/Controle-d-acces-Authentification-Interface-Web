<?php
// Déconnexion : on vide et on détruit la session
require_once __DIR__ . '/includes/session.php';

// Vide toutes les données de la session
$_SESSION = [];

// Supprime le cookie de session dans le navigateur
$parametres = session_get_cookie_params();
setcookie(session_name(), '', time() - 3600, $parametres['path']);

// Détruit la session côté serveur
session_destroy();

header('Location: connexion.php');
exit;