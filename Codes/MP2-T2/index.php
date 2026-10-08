<?php
// Page d'accueil : réservée aux utilisateurs connectés
require_once __DIR__ . '/includes/session.php';

exiger_connexion();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle 215 — Accueil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="carte">
        <h1>Salle 215</h1>
        <p>Bonjour <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></p>
        <p>Rôle : <?= htmlspecialchars($_SESSION['role']) ?></p>

        <p><a href="deconnexion.php">Se déconnecter</a></p>
    </main>
</body>
</html>