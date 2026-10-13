<?php
// Haut de page commun : à inclure au début de chaque page
// Avant de l'inclure, la page définit $titre (ex. : $titre = 'Accueil';)
require_once __DIR__ . '/session.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle 215 — <?= htmlspecialchars($titre ?? '') ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="menu">
        <span class="menu-titre">Salle 215</span>

        <?php if (est_connecte()): ?>
            <a href="index.php">Accueil</a>

            <?php if (est_admin()): ?>
                <a href="utilisateurs.php">Utilisateurs</a>
            <?php endif; ?>

            <span class="menu-droite">
                <?= htmlspecialchars($_SESSION['prenom']) ?>
                (<?= htmlspecialchars($_SESSION['role']) ?>)
                <a href="deconnexion.php">Déconnexion</a>
            </span>
        <?php endif; ?>
    </nav>