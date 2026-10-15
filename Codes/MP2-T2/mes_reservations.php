<?php
require_once 'includes/db.php';
require_once 'includes/session.php';
exiger_connexion();

$pdo = connexion_bdd();
$requete = $pdo->prepare('SELECT r.id, r.date_debut, r.date_fin, q.code
                          FROM reservations r
                          LEFT JOIN qrcodes q ON q.id_reservation = r.id
                          WHERE r.id_utilisateur = ?
                          ORDER BY r.date_debut DESC');
$requete->execute([$_SESSION['id_utilisateur']]);
$reservations = $requete->fetchAll();

// Met un antislash devant les virgules et points-virgules,
// comme le demande le format iCalendar
function icalEchap($texte)
{
    return str_replace([',', ';'], ['\,', '\;'], $texte);
}

$titre = 'Mes réservations';
require_once 'includes/entete.php';
?>
    <main class="carte large">
        <h1>Mes réservations</h1>
        <p><a href="reservation.php">+ Réserver un créneau</a></p>
        <?php if (count($reservations) == 0) { ?>
            <p>Vous n'avez aucune réservation.</p>
        <?php } else { ?>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>QR code</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($reservations as $r) {
                    $debut = date('Ymd\THis', strtotime($r['date_debut']));
                    $fin = date('Ymd\THis', strtotime($r['date_fin']));
                    $lieu = icalEchap('Rue Emile Singla / Avenue de Saint-Pierre, 12000 Rodez');

                    $evenement_agenda = "BEGIN:VCALENDAR\r\n"
                                       . "VERSION:2.0\r\n"
                                       . "BEGIN:VEVENT\r\n"
                                       . "SUMMARY:Reservation salle 215 - Lycee Carnus\r\n"
                                       . "LOCATION:$lieu\r\n"
                                       . "DTSTART:$debut\r\n"
                                       . "DTEND:$fin\r\n"
                                       . "END:VEVENT\r\n"
                                       . "END:VCALENDAR";
                ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($r['date_debut'])); ?></td>
                        <td><?php echo date('H:i', strtotime($r['date_debut'])); ?></td>
                        <td><?php echo date('H:i', strtotime($r['date_fin'])); ?></td>
                        <td>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($evenement_agenda); ?>" alt="QR code" width="80" height="80">
                        </td>
                        <td>
                            <form method="post" action="reservation_annuler.php" class="en-ligne"
                                  onsubmit="return confirm('Annuler cette réservation ?');">
                                <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                <button type="submit" class="petit-bouton">Annuler</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </main>
</body>
</html>