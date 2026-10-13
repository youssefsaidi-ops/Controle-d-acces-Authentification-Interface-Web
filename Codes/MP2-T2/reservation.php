<?php
// Page pour voir les disponibilités et réserver un créneau
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_connexion();

$erreur = '';
$pdo = connexion_bdd();

// --- Traitement du formulaire de réservation ---
if (isset($_POST['date_debut'])) {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if ($date_fin <= $date_debut) {
        $erreur = 'L\'heure de fin doit être après l\'heure de début.';
    } else {
        $requete = $pdo->prepare('SELECT id FROM reservations
                                  WHERE date_debut < ? AND date_fin > ?');
        $requete->execute([$date_fin, $date_debut]);

        if ($requete->fetch()) {
            $erreur = 'Ce créneau est déjà réservé.';
        } else {
            $requete = $pdo->prepare('INSERT INTO reservations (id_utilisateur, date_debut, date_fin)
                                      VALUES (?, ?, ?)');
            $requete->execute([$_SESSION['id_utilisateur'], $date_debut, $date_fin]);

            $id_reservation = $pdo->lastInsertId();
            $code = bin2hex(random_bytes(16));

            $requete = $pdo->prepare('INSERT INTO qrcodes (code, id_reservation) VALUES (?, ?)');
            $requete->execute([$code, $id_reservation]);

            header('Location: mes_reservations.php');
            exit;
        }
    }
}

// --- Date choisie pour le calendrier (aujourd'hui par défaut) ---
if (isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = date('Y-m-d');
}

// On récupère les réservations de ce jour-là
$requete = $pdo->prepare('SELECT r.date_debut, r.date_fin, u.nom, u.prenom
                          FROM reservations r
                          INNER JOIN utilisateurs u ON u.id = r.id_utilisateur
                          WHERE DATE(r.date_debut) = ?
                          ORDER BY r.date_debut');
$requete->execute([$date]);
$reservations_du_jour = $requete->fetchAll();

// On construit les créneaux d'une heure, de 8h à 20h
$creneaux = [];
for ($heure = 8; $heure < 20; $heure++) {
    $debut_creneau = $date . ' ' . sprintf('%02d:00:00', $heure);
    $fin_creneau = $date . ' ' . sprintf('%02d:00:00', $heure + 1);

    $occupe = false;
    $nom_occupant = '';

    foreach ($reservations_du_jour as $r) {
        // Le créneau est occupé s'il chevauche une réservation existante
        if ($debut_creneau < $r['date_fin'] && $fin_creneau > $r['date_debut']) {
            $occupe = true;
            $nom_occupant = $r['prenom'] . ' ' . $r['nom'];
        }
    }

    $creneaux[] = [
        'heure' => $heure,
        'debut' => $debut_creneau,
        'fin' => $fin_creneau,
        'occupe' => $occupe,
        'nom' => $nom_occupant,
    ];
}

$titre = 'Réserver la salle 215';
require_once 'includes/entete.php';
?>
    <main class="carte large">
        <h1>Réserver la salle 215</h1>

        <?php if ($erreur != '') { ?>
            <p class="erreur"><?php echo $erreur; ?></p>
        <?php } ?>

        <form method="get" class="form-date">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>"
                   onchange="this.form.submit()">
        </form>

        <div class="calendrier">
            <?php foreach ($creneaux as $c) { ?>
                <?php if ($c['occupe']) { ?>
                    <div class="creneau creneau-occupe">
                        <strong><?php echo sprintf('%02d:00', $c['heure']); ?></strong>
                        <span class="creneau-detail">Occupé<br><?php echo htmlspecialchars($c['nom']); ?></span>
                    </div>
                <?php } else { ?>
                    <button type="button" class="creneau creneau-libre"
                            onclick="choisirCreneau('<?php echo $c['debut']; ?>', '<?php echo $c['fin']; ?>')">
                        <strong><?php echo sprintf('%02d:00', $c['heure']); ?></strong>
                        <span class="creneau-detail">Libre</span>
                    </button>
                <?php } ?>
            <?php } ?>
        </div>

        <form method="post" class="form-reservation">
            <label for="date_debut">Début</label>
            <input type="datetime-local" id="date_debut" name="date_debut" required>

            <label for="date_fin">Fin</label>
            <input type="datetime-local" id="date_fin" name="date_fin" required>

            <button type="submit">Réserver</button>
        </form>
    </main>

    <script>
        // Remplit le formulaire quand on clique sur un créneau libre
        function choisirCreneau(debut, fin) {
            document.getElementById('date_debut').value = debut.replace(' ', 'T').substring(0, 16);
            document.getElementById('date_fin').value = fin.replace(' ', 'T').substring(0, 16);
        }
    </script>
</body>
</html>