<?php
// Page d'accueil : réservée aux utilisateurs connectés
require_once __DIR__ . '/includes/session.php';

exiger_connexion();

$titre = 'Accueil';
require __DIR__ . '/includes/entete.php';
?>

<main class="carte">
    <h1>Bienvenue <?= htmlspecialchars($_SESSION['prenom']) ?></h1>
    <p>Réservation de la salle 215.</p>
</main>

</body>
</html>