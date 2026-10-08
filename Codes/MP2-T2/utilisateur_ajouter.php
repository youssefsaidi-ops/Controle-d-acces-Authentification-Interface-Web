<?php
// Ajout d'un utilisateur : réservé à l'administrateur
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

exiger_admin();

$pdo = connexion_bdd();
$roles = $pdo->query('SELECT id, nom FROM roles ORDER BY id')->fetchAll();

$erreur = '';
$nom = '';
$prenom = '';
$email = '';
$id_role = 3; // élève par défaut

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $id_role = (int) ($_POST['id_role'] ?? 3);

    // Vérification des données côté serveur
    if ($nom === '' || $prenom === '' || $email === '' || $mot_de_passe === '') {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
    } elseif (strlen($mot_de_passe) < 8) {
        $erreur = 'Le mot de passe doit faire au moins 8 caractères.';
    } else {
        // L'email doit être unique
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $requete->execute([$email]);

        if ($requete->fetch()) {
            $erreur = 'Cet email est déjà utilisé.';
        } else {
            $requete = $pdo->prepare(
                'INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_role)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $requete->execute([
                $nom,
                $prenom,
                $email,
                password_hash($mot_de_passe, PASSWORD_DEFAULT),
                $id_role
            ]);

            header('Location: utilisateurs.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle 215 — Ajouter un utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="carte">
        <h1>Ajouter un utilisateur</h1>
        <p><a href="utilisateurs.php">← Retour à la liste</a></p>

        <?php if ($erreur !== ''): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="mot_de_passe">Mot de passe (8 caractères minimum)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <label for="id_role">Rôle</label>
            <select id="id_role" name="id_role">
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= $role['id'] == $id_role ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Ajouter</button>
        </form>
    </main>
</body>
</html>