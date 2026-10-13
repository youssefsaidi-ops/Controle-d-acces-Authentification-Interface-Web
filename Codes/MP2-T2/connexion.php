<?php
// Page de connexion
require_once 'includes/db.php';
require_once 'includes/session.php';

if (est_connecte()) {
    header('Location: index.php');
    exit;
}

// On crée un jeton anti-CSRF s'il n'existe pas encore
if (!isset($_SESSION['jeton_csrf'])) {
    $_SESSION['jeton_csrf'] = bin2hex(random_bytes(32));
}

// On initialise le compteur de tentatives ratées
if (!isset($_SESSION['tentatives_ratees'])) {
    $_SESSION['tentatives_ratees'] = 0;
}

$erreur = '';

// On bloque après 5 tentatives ratées, pendant 1 minute
$bloque = false;
if ($_SESSION['tentatives_ratees'] >= 5) {
    $temps_ecoule = time() - $_SESSION['derniere_tentative'];
    if ($temps_ecoule < 60) {
        $bloque = true;
        $erreur = 'Trop de tentatives. Réessayez dans ' . (60 - $temps_ecoule) . ' secondes.';
    } else {
        $_SESSION['tentatives_ratees'] = 0;
    }
}

if (!$bloque && isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
    if (!isset($_POST['jeton_csrf']) || $_POST['jeton_csrf'] !== $_SESSION['jeton_csrf']) {
        $erreur = 'Jeton de sécurité invalide. Rechargez la page et réessayez.';
    } else {
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        $pdo = connexion_bdd();

        $requete = $pdo->prepare('SELECT u.id, u.nom, u.prenom, u.mot_de_passe, r.nom AS role
                                  FROM utilisateurs u
                                  INNER JOIN roles r ON r.id = u.id_role
                                  WHERE u.email = ?');
        $requete->execute([$email]);
        $utilisateur = $requete->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            $_SESSION['tentatives_ratees'] = 0;

            $_SESSION['id_utilisateur'] = $utilisateur['id'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['role'] = $utilisateur['role'];

            header('Location: index.php');
            exit;
        } else {
            $_SESSION['tentatives_ratees']++;
            $_SESSION['derniere_tentative'] = time();
            $erreur = 'Email ou mot de passe incorrect.';
        }
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
        <?php if (!$bloque) { ?>
            <form method="post">
                <input type="hidden" name="jeton_csrf" value="<?php echo $_SESSION['jeton_csrf']; ?>">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <button type="submit">Se connecter</button>
            </form>
            <p><a href="inscription.php">Créer un compte</a></p>
        <?php } ?>
    </main>
</body>
</html>