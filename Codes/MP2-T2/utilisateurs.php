<?php
// Liste des utilisateurs : réservée à l'administrateur
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

exiger_admin();

$pdo = connexion_bdd();
$utilisateurs = $pdo->query(
    'SELECT u.id, u.nom, u.prenom, u.email, r.nom AS role
     FROM utilisateurs u
     INNER JOIN roles r ON r.id = u.id_role
     ORDER BY u.nom, u.prenom'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle 215 — Utilisateurs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="carte large">
        <h1>Utilisateurs</h1>
        <p><a href="index.php">← Accueil</a></p>
        <p><a href="utilisateur_ajouter.php">+ Ajouter un utilisateur</a></p>

        <table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($utilisateurs as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['prenom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['role']) ?></td>
                    <td>
                        <a href="utilisateur_modifier.php?id=<?= $u['id'] ?>">Modifier</a>

                        <?php if ($u['id'] != $_SESSION['id_utilisateur']): ?>
                            <form method="post" action="utilisateur_supprimer.php" class="en-ligne"
                                  onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="petit-bouton">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>