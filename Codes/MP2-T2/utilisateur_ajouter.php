<?php
// Ajout d'un utilisateur : réservé à l'admin
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_admin();

$erreur = '';

// Le formulaire a été envoyé
if (isset($_POST['nom'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $id_role = $_POST['id_role'];

    $pdo = connexion_bdd();

    // On vérifie que l'email n'est pas déjà utilisé
    $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
    $requete->execute([$email]);

    if (strlen($mot_de_passe) < 8) {
        $erreur = 'Le mot de passe doit faire au moins 8 caractères.';
    } elseif ($requete->fetch()) {
        $erreur = 'Cet email est déjà utilisé.';
    } else {
        // Le mot de passe est haché avant d'être enregistré
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $requete = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_role)
                                  VALUES (?, ?, ?, ?, ?)');
        $requete->execute([$nom, $prenom, $email, $hash, $id_role]);

        header('Location: utilisateurs.php');
        exit;
    }
}

$titre = 'Ajouter un utilisateur';
require_once 'includes/entete.php';
?>
    <main class="carte">
        <h1>Ajouter un utilisateur</h1>
        <p><a href="utilisateurs.php">← Retour à la liste</a></p>

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

            <label for="mot_de_passe">Mot de passe (8 caractères minimum)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <label for="id_role">Rôle</label>
            <select id="id_role" name="id_role">
                <option value="2">client</option>
                <option value="1">admin</option>
            </select>

            <button type="submit">Ajouter</button>
        </form>
    </main>
</body>
</html>