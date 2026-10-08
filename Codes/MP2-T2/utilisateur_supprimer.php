<?php
// Suppression d'un utilisateur : réservé à l'administrateur
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

exiger_admin();

// On n'accepte que les formulaires envoyés en POST (pas un simple lien)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Méthode non autorisée.');
}

$id = (int) ($_POST['id'] ?? 0);

// L'admin ne peut pas supprimer son propre compte
if ($id === (int) $_SESSION['id_utilisateur']) {
    exit('Vous ne pouvez pas supprimer votre propre compte.');
}

$pdo = connexion_bdd();

// Grâce aux clés étrangères (ON DELETE CASCADE), ses réservations
// et ses badges sont supprimés aussi
$requete = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
$requete->execute([$id]);

header('Location: utilisateurs.php');
exit;