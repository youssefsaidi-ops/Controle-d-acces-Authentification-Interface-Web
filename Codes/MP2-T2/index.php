<?php
// Page d'accueil : réservée aux utilisateurs connectés
require_once 'includes/session.php';

exiger_connexion();

$titre = 'Accueil';
require_once 'includes/entete.php';
?>
    <main class="carte">
        <h1>Bienvenue <?php echo htmlspecialchars($_SESSION['prenom']); ?></h1>
        <p>Réservation de la salle 215.</p>
    </main>
</body>
</html>