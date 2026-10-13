<?php
// Remplace les mots de passe de test ("test_a_remplacer") par un vrai mot de passe haché.
// À lancer une seule fois, depuis le dossier Codes\MP2-T2 :
//   D:\xampp\php\php.exe outils\init_mots_de_passe.php
require_once 'includes/db.php';

$hash = password_hash('Test1234!', PASSWORD_DEFAULT);

$pdo = connexion_bdd();
$requete = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE mot_de_passe = ?');
$requete->execute([$hash, 'test_a_remplacer']);

echo $requete->rowCount() . " utilisateur(s) mis à jour.\n";