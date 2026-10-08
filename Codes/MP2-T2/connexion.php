<?php
// Page de connexion
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

// Déjà connecté : on va directement à l'accueil
if (est_connecte()) {
    header('Location: index.php');
    exit;
}

$erreur = '';
$email = '';

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $mot_de_passe === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $pdo = connexion_bdd();

        // On cherche l'utilisateur et le nom de son rôle
        $requete = $pdo->prepare(
            'SELECT u.id, u.nom, u.prenom, u.mot_de_passe, r.nom AS role
             FROM utilisateurs u
             INNER JOIN roles r ON r.id = u.id_role
             WHERE u.email = ?'
        );
        $requete->execute([$email]);
        $utilisateur = $requete->fetch();

        // password_verify compare le mot de passe tapé avec le hash
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Nouvel identifiant de session pour éviter le vol de session
            session_regenerate_id(true);

            $_SESSION['id_utilisateur'] = $utilisateur['id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['role'] = $utilisateur['role'];

            header('Location: index.php');
            exit;
        }

        // Même message que l'email ou le mot de passe soit faux
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
$titre = 'Connexion';
require __DIR__ . '/includes/entete.php';

?>
    <main class="carte">
        <h1>Connexion</h1>

        <?php if ($erreur !== ''): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <button type="submit">Se connecter</button>
        </form>
    </main>
</body>
</html>