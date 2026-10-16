<?php
// Annulation d'une réservation
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_connexion();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $pdo = connexion_bdd();

    $pdo = connexion_bdd();

    // On récupère la date AVANT de supprimer, sinon on la perd
    $requete = $pdo->prepare('SELECT date_debut, date_fin FROM reservations WHERE id = ? AND id_utilisateur = ?');
    $requete->execute([$id, $_SESSION['id_utilisateur']]);
    $reservation = $requete->fetch();

    // On ne peut annuler que SES PROPRES réservations
    $requete = $pdo->prepare('DELETE FROM reservations WHERE id = ? AND id_utilisateur = ?');
    $requete->execute([$id, $_SESSION['id_utilisateur']]);

    if ($reservation) {
        $date = date('d/m/Y', strtotime($reservation['date_debut']));
        $heureDebut = date('H:i', strtotime($reservation['date_debut']));
        $heureFin = date('H:i', strtotime($reservation['date_fin']));
        ajouterJournal($pdo, $_SESSION['id_utilisateur'], 'annulation_reservation', "Réservation annulée : $date de $heureDebut à $heureFin");
    }
}

header('Location: mes_reservations.php');
exit;