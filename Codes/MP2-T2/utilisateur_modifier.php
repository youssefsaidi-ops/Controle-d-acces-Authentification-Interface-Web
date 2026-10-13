<?php
// Modification d'un utilisateur : réservé à l'admin
require_once 'includes/db.php';
require_once 'includes/session.php';

exiger_admin();

$pdo = connexion_bdd();
$erreur = '';

// L'id de l'utilisateur à modifier est dans l'adresse : utilisateur_modifier.php?id=2
$id = $_GET['id'];

// Le formulaire a été envoyé
if (isset($_POST['nom'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $id_role = $_POST['id_role'];

    // L'admin ne peut pas s'enlever le rôle admin à lui-même
    if ($id == $_SESSION['id_utilisateur']) {
        $id_role = 1;
    }

    // On vérifie que l'email n'est pas utilisé par un AUTRE utilisateur
    $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id != ?');
    $requete->execute([$email, $id]);

    if ($mot_de_passe != '' && strlen($mot_de_passe) < 8) {
        $erreur = 'Le mot de passe doit faire au moins 8 caractères.';
    } elseif ($requete->fetch()) {
        $erreur = 'Cet email est déjà utilisé.';
    } else {
        $requete = $pdo->prepare('UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, id_role = ? WHERE id = ?');
        $requete->execute([$nom, $prenom, $email, $id_role, $id]);

        // On change le mot de passe seulement si un nouveau a été tapé
        if ($mot_de_passe != '') {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $requete = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?');
            $requete->execute([$hash, $id]);
        }

        ajouterJournal($pdo, $_SESSION['id_utilisateur'], 'modification_utilisateur', "Utilisateur modifié : $prenom $nom ($email)");

        header('Location: utilisateurs.php');
        exit;
    }
}

// On récupère les infos actuelles de l'utilisateur pour remplir le formulaire
$requete = $pdo->prepare('SELECT nom, prenom, email, id_role FROM utilisateurs WHERE id = ?');
$requete->execute([$id]);
$utilisateur = $requete->fetch();

if (!$utilisateur) {
    echo 'Utilisateur introuvable.';
    exit;
}

$titre = 'Modifier un utilisateur';
require_once 'includes/entete.php';
?>
    <main class="carte">
        <h1>Modifier un utilisateur</h1>
        <p><a href="utilisateurs.php">← Retour à la liste</a></p>

        <?php if ($erreur != '') { ?>
            <p class="erreur"><?php echo $erreur; ?></p>
        <?php } ?>

        <form method="post">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($utilisateur['nom']); ?>" required>

            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($utilisateur['prenom']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required>

            <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas le changer)</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe">

            <label for="id_role">Rôle</label>
            <select id="id_role" name="id_role">
                <option value="2" <?php if ($utilisateur['id_role'] == 2) { echo 'selected'; } ?>>client</option>
                <option value="1" <?php if ($utilisateur['id_role'] == 1) { echo 'selected'; } ?>>admin</option>
            </select>

            <button type="submit">Enregistrer</button>
        </form>
    </main>
</body>
</html>