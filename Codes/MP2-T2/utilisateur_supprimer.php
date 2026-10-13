<?php
// Suppression d'un utilisateur : réservé à l'admin
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_admin();

// Le formulaire de la liste envoie l'id en POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // L'admin ne peut pas supprimer son propre compte
    if ($id == $_SESSION['id_utilisateur']) {
        echo 'Vous ne pouvez pas supprimer votre propre compte.';
        exit;
    }

    $pdo = connexion_bdd();
    $requete = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
    $requete->execute([$id]);
}

header('Location: utilisateurs.php');
exit;