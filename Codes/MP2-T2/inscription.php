<?php
// Page pour créer un compte (rôle client par défaut)
require_once 'includes/db.php';
require_once 'includes/session.php';

if (est_connecte()) {
    header('Location: index.php');
    exit;
}

$erreur = '';
$pdo = connexion_bdd();

if (isset($_POST['email'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    if (strlen($mot_de_passe) < 8) {
        $erreur = 'Le mot de passe doit faire au moins 8 caractères.';
    } else {
        // On vérifie que l'email n'est pas déjà utilisé
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $requete->execute([$email]);

        if ($requete->fetch()) {
            $erreur = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // id_role = 2 = client
            $requete = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_role)
                                      VALUES (?, ?, ?, ?, 2)');
            $requete->execute([$nom, $prenom, $email, $hash]);

            header('Location: connexion.php');
            exit;
        }
    }
}

$titre = 'Créer un compte';
require_once 'includes/entete.php';
?>
    <main class="carte">
        <h1>Créer un compte</h1>
        <?php if ($erreur != '') { ?>
            <p class="erreur"><?php echo $erreur; ?></p>
        <?php } ?>
        <form method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            <button type="submit">Créer le compte</button>
        </form>
        <p><a href="connexion.php">← Retour à la connexion</a></p>
    </main>
</body>
</html>