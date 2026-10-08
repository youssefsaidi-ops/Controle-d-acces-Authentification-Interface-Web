<?php
// Remplace les mots de passe de test par un vrai mot de passe haché.
// À lancer UNE fois, en ligne de commande uniquement :
//   D:\xampp\php\php.exe Codes\MP2-T2\outils\init_mots_de_passe.php

// Interdit l'exécution depuis le navigateur
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Accès interdit.');
}

require_once __DIR__ . '/../includes/db.php';

$mot_de_passe_test = 'Test1234!';
$hash = password_hash($mot_de_passe_test, PASSWORD_DEFAULT);

$pdo = connexion_bdd();

// Requête préparée : la valeur est envoyée séparément de la requête SQL
$requete = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE mot_de_passe = ?');
$requete->execute([$hash, 'test_a_remplacer']);

echo $requete->rowCount() . " utilisateur(s) mis à jour.\n";
echo "Mot de passe de test : $mot_de_passe_test\n";