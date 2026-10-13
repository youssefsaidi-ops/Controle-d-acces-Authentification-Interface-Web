<?php
// Page affichée quand on scanne le QR code : lieu, date, heure
require_once 'includes/db.php';

$code = $_GET['code'];

$pdo = connexion_bdd();
$requete = $pdo->prepare('SELECT r.date_debut, r.date_fin, u.prenom
                          FROM qrcodes q
                          INNER JOIN reservations r ON r.id = q.id_reservation
                          INNER JOIN utilisateurs u ON u.id = r.id_utilisateur
                          WHERE q.code = ?');
$requete->execute([$code]);
$reservation = $requete->fetch();

if (!$reservation) {
    echo 'Réservation introuvable.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle 215 — Ma réservation</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="carte">
        <h1>Salle 215</h1>
        <p>Réservation de <?php echo htmlspecialchars($reservation['prenom']); ?></p>
        <p><strong>Date :</strong> <?php echo date('d/m/Y', strtotime($reservation['date_debut'])); ?></p>
        <p><strong>Horaire :</strong>
            <?php echo date('H:i', strtotime($reservation['date_debut'])); ?>
            —
            <?php echo date('H:i', strtotime($reservation['date_fin'])); ?>
        </p>

        <p><a href="qr_agenda.php?code=<?php echo htmlspecialchars($code); ?>">📅 Ajouter à l'agenda</a></p>
    </main>
</body>
</html>