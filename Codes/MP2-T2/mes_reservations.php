<?php
// Liste des réservations de l'utilisateur connecté
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_connexion();

$pdo = connexion_bdd();

// On n'affiche que les réservations de la personne connectée
$requete = $pdo->prepare('SELECT r.id, r.date_debut, r.date_fin, q.code
                          FROM reservations r
                          LEFT JOIN qrcodes q ON q.id_reservation = r.id
                          WHERE r.id_utilisateur = ?
                          ORDER BY r.date_debut DESC');
$requete->execute([$_SESSION['id_utilisateur']]);
$reservations = $requete->fetchAll();

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
                <?php foreach ($reservations as $r) { ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($r['date_debut'])); ?></td>
                        <td><?php echo date('H:i', strtotime($r['date_debut'])); ?></td>
                        <td><?php echo date('H:i', strtotime($r['date_fin'])); ?></td>
                        <td>
                            <a href="qr.php?code=<?php echo htmlspecialchars($r['code']); ?>">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?php echo urlencode(SITE_URL . '/qr.php?code=' . $r['code']); ?>" alt="QR code">
                            </a>
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