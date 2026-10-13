<?php
// Gestion de la session : savoir qui est connecté
session_start();

// Renvoie vrai si quelqu'un est connecté
function est_connecte()
{
    return isset($_SESSION['id_utilisateur']);
}

// Renvoie vrai si la personne connectée est admin
function est_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Si personne n'est connecté, on renvoie vers la page de connexion
function exiger_connexion()
{
    if (!est_connecte()) {
        header('Location: connexion.php');
        exit;
    }
}

// Si la personne n'est pas admin, on bloque la page
function exiger_admin()
{
    exiger_connexion();
    if (!est_admin()) {
        echo 'Accès réservé à l\'administrateur.';
        exit;
    }
}