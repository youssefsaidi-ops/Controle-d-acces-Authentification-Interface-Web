<?php
// Réservation d'un créneau pour la salle 215
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_connexion();

$erreur = '';
$pdo = connexion_bdd();

// Le formulaire a été envoyé
if (isset($_POST['date_debut'])) {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if ($date_fin <= $date_debut) {
        $erreur = 'L\'heure de fin doit être après l\'heure de début.';
    } else {
        // On vérifie qu'aucune réservation existante ne chevauche ce créneau
        $requete = $pdo->prepare('SELECT id FROM reservations
                                  WHERE date_debut < ? AND date_fin > ?');
        $requete->execute([$date_fin, $date_debut]);

        if ($requete->fetch()) {
            $erreur = 'Ce créneau est déjà réservé.';
        } else {
            $requete = $pdo->prepare('INSERT INTO reservations (id_utilisateur, date_debut, date_fin)
                                      VALUES (?, ?, ?)');
            $requete->execute([$_SESSION['id_utilisateur'], $date_debut, $date_fin]);

            header('Location: mes_reservations.php');
            exit;
        }
    }
}

$titre = 'Réserver un créneau';
require_once 'includes/entete.php';
?>
    <main class="carte">
        <h1>Réserver la salle 215</h1>
        <p><a href="disponibilites.php">← Voir les disponibilités</a></p>

        <?php if ($erreur != '') { ?>
            <p class="erreur"><?php echo $erreur; ?></p>
        <?php } ?>

        <form method="post">
            <label for="date_debut">Début</label>
            <input type="datetime-local" id="date_debut" name="date_debut" required>

            <label for="date_fin">Fin</label>
            <input type="datetime-local" id="date_fin" name="date_fin" required>

            <button type="submit">Réserver</button>
        </form>
    </main>
</body>
</html>