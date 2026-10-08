<?php
// Modification d'un utilisateur : réservé à l'administrateur
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

exiger_admin();

$pdo = connexion_bdd();
$roles = $pdo->query('SELECT id, nom FROM roles ORDER BY id')->fetchAll();

// On récupère l'utilisateur à modifier grâce à l'id dans l'adresse
$id = (int) ($_GET['id'] ?? 0);
$requete = $pdo->prepare('SELECT id, nom, prenom, email, id_role FROM utilisateurs WHERE id = ?');
$requete->execute([$id]);
$utilisateur = $requete->fetch();

if (!$utilisateur) {
    http_response_code(404);
    exit('Utilisateur introuvable.');
}

// Vrai si l'admin est en train de modifier son propre compte
$est_moi = ($id === (int) $_SESSION['id_utilisateur']);

$erreur = '';
$nom = $utilisateur['nom'];
$prenom = $utilisateur['prenom'];
$email = $utilisateur['email'];
$id_role = $utilisateur['id_role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $id_role = (int) ($_POST['id_role'] ?? 3);

    // L'admin ne peut pas changer son propre rôle (sinon il perd l'accès)
    if ($est_moi) {
        $id_role = $utilisateur['id_role'];
    }

    if ($nom === '' || $prenom === '' || $email === '') {
        $erreur = 'Le nom, le prénom et l\'email sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
    } elseif ($mot_de_passe !== '' && strlen($mot_de_passe) < 8) {
        $erreur = 'Le mot de passe doit faire au moins 8 caractères.';
    } else {
        // L'email doit être unique (sauf pour l'utilisateur lui-même)
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id <> ?');
        $requete->execute([$email, $id]);

        if ($requete->fetch()) {
            $erreur = 'Cet email est déjà utilisé.';
        } else {
            $requete = $pdo->prepare(
                'UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, id_role = ? WHERE id = ?'
            );
            $requete->execute([$nom, $prenom, $email, $id_role, $id]);

            // Le mot de passe n'est changé que si un nouveau a été tapé
            if ($mot_de_passe !== '') {
                $requete = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?');
                $requete->execute([password_hash($mot_de_passe, PASSWORD_DEFAULT), $id]);
            }

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
    <title>Salle 215 — Modifier un utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="carte">
        <h1>Modifier un utilisateur</h1>
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

            <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe">

            <label for="id_role">Rôle</label>
            <select id="id_role" name="id_role" <?= $est_moi ? 'disabled' : '' ?>>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= $role['id'] == $id_role ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enregistrer</button>
        </form>
    </main>
</body>
</html>