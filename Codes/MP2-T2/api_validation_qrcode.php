<?php
// API appelée par le lecteur à l'entrée de la salle pour valider un QR code
require_once 'includes/db.php';

header('Content-Type: application/json');

$pdo = connexion_bdd();

$code = $_POST['code'];

// On cherche le QR code et la réservation liée
$requete = $pdo->prepare('SELECT r.id_utilisateur, r.date_debut, r.date_fin
                          FROM qrcodes q
                          INNER JOIN reservations r ON r.id = q.id_reservation
                          WHERE q.code = ?');
$requete->execute([$code]);
$reservation = $requete->fetch();

if (!$reservation) {
    $resultat = 'inconnu';
    $id_utilisateur = null;
} else {
    $maintenant = date('Y-m-d H:i:s');

    // Le code n'est valide que pendant le créneau réservé
    if ($maintenant >= $reservation['date_debut'] && $maintenant <= $reservation['date_fin']) {
        $resultat = 'autorise';
    } else {
        $resultat = 'refuse';
    }

    $id_utilisateur = $reservation['id_utilisateur'];
}

// On enregistre la tentative dans le journal d'accès
$requete = $pdo->prepare('INSERT INTO acces (type, valeur_lue, id_utilisateur, resultat)
                          VALUES (?, ?, ?, ?)');
$requete->execute(['QR', $code, $id_utilisateur, $resultat]);

echo json_encode(['resultat' => $resultat]);