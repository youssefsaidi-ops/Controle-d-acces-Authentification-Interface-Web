<?php
// Annulation d'une réservation
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_connexion();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $pdo = connexion_bdd();

    // On ne peut annuler que SES PROPRES réservations
    $requete = $pdo->prepare('DELETE FROM reservations WHERE id = ? AND id_utilisateur = ?');
    $requete->execute([$id, $_SESSION['id_utilisateur']]);
}

header('Location: mes_reservations.php');
exit;