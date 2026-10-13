<?php
// Consultation des disponibilités de la salle 215
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_connexion();

// Si aucune date n'est choisie, on prend aujourd'hui par défaut
if (isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = date('Y-m-d');
}

$pdo = connexion_bdd();

// On cherche les réservations du jour choisi, avec le nom de la personne
$requete = $pdo->prepare('SELECT r.date_debut, r.date_fin, u.nom, u.prenom
                          FROM reservations r
                          INNER JOIN utilisateurs u ON u.id = r.id_utilisateur
                          WHERE DATE(r.date_debut) = ?
                          ORDER BY r.date_debut');
$requete->execute([$date]);
$reservations = $requete->fetchAll();

$titre = 'Disponibilités';
require_once 'includes/entete.php';
?>
    <main class="carte large">
        <h1>Disponibilités — Salle 215</h1>

        <form method="get">
            <label for="date">Choisir une date</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            <button type="submit">Voir</button>
        </form>

        <?php if (count($reservations) == 0) { ?>
            <p class="succes">Aucune réservation ce jour-là : la salle est libre.</p>
        <?php } else { ?>
            <table>
                <tr>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Réservé par</th>
                </tr>
                <?php foreach ($reservations as $r) { ?>
                    <tr>
                        <td><?php echo date('H:i', strtotime($r['date_debut'])); ?></td>
                        <td><?php echo date('H:i', strtotime($r['date_fin'])); ?></td>
                        <td><?php echo htmlspecialchars($r['prenom'] . ' ' . $r['nom']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>

        <p><a href="reservation.php">+ Réserver un créneau</a></p>
    </main>
</body>
</html>