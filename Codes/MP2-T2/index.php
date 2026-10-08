<?php
// Page de test : vérifie la connexion à la base
require_once __DIR__ . '/includes/db.php';

$pdo = connexion_bdd();
$roles = $pdo->query('SELECT id, nom FROM roles ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle 215 — Test connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="carte">
        <h1>Salle 215</h1>
        <p class="succes">Connexion à la base réussie</p>
        <h2>Rôles trouvés</h2>
        <ul>
            <?php foreach ($roles as $role): ?>
                <li><?= htmlspecialchars($role['id'] . ' — ' . $role['nom']) ?></li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>