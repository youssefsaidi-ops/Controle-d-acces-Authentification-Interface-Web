<?php
// Page de connexion
require_once 'includes/db.php';
require_once 'includes/session.php';

// Si on est déjà connecté, on va directement à l'accueil
if (est_connecte()) {
    header('Location: index.php');
    exit;
}

$erreur = '';

// Le formulaire a été envoyé
if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $pdo = connexion_bdd();

    // On cherche l'utilisateur avec cet email (et le nom de son rôle)
    $requete = $pdo->prepare('SELECT u.id, u.nom, u.prenom, u.mot_de_passe, r.nom AS role
                              FROM utilisateurs u
                              INNER JOIN roles r ON r.id = u.id_role
                              WHERE u.email = ?');
    $requete->execute([$email]);
    $utilisateur = $requete->fetch();

    // password_verify compare le mot de passe tapé avec le hash de la base
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        // On enregistre l'utilisateur dans la session
        $_SESSION['id_utilisateur'] = $utilisateur['id'];
        $_SESSION['nom'] = $utilisateur['nom'];
        $_SESSION['prenom'] = $utilisateur['prenom'];
        $_SESSION['role'] = $utilisateur['role'];

        header('Location: index.php');
        exit;
    } else {
        // Même message si l'email ou le mot de passe est faux
        $erreur = 'Email ou mot de passe incorrect.';
    }
}

$titre = 'Connexion';
require_once 'includes/entete.php';
?>
    <main class="carte">
        <h1>Connexion</h1>

        <?php if ($erreur != '') { ?>
            <p class="erreur"><?php echo $erreur; ?></p>
        <?php } ?>

            <form method="post">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <button type="submit">Se connecter</button>
            </form>
            <p><a href="inscription.php">Créer un compte</a></p>
    </main>

</body>
</html>