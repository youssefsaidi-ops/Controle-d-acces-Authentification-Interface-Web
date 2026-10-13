<?php
// Génère un fichier .ics à ajouter dans l'agenda du téléphone
require_once 'includes/db.php';

$code = $_GET['code'];

$pdo = connexion_bdd();
$requete = $pdo->prepare('SELECT date_debut, date_fin FROM qrcodes q
                          INNER JOIN reservations r ON r.id = q.id_reservation
                          WHERE q.code = ?');
$requete->execute([$code]);
$reservation = $requete->fetch();

if (!$reservation) {
    echo 'Réservation introuvable.';
    exit;
}

// Les dates dans un fichier .ics s'écrivent sous la forme AAAAMMJJTHHMMSS
$debut = date('Ymd\THis', strtotime($reservation['date_debut']));
$fin = date('Ymd\THis', strtotime($reservation['date_fin']));

// On dit au navigateur que c'est un fichier à télécharger
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="reservation-salle-215.ics"');

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "BEGIN:VEVENT\r\n";
echo "SUMMARY:Réservation salle 215\r\n";
echo "LOCATION:Salle 215\r\n";
echo "DTSTART:$debut\r\n";
echo "DTEND:$fin\r\n";
echo "END:VEVENT\r\n";
echo "END:VCALENDAR\r\n";