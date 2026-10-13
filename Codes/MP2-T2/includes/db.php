<?php
// Connexion à la base de données avec PDO
require_once __DIR__ . '/../config.php';
function connexion_bdd()
{
    $port = defined('DB_PORT') ? DB_PORT : 3306;

    try {
        $pdo = new PDO('mysql:host=' . DB_HOTE . ';port=' . $port . ';dbname=' . DB_NOM . ';charset=utf8mb4', DB_UTILISATEUR, DB_MOT_DE_PASSE);
        // Si une requête SQL échoue, PHP affiche une erreur
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // On n'affiche pas le détail de l'erreur (il peut contenir des infos sur le serveur)
        die('Erreur de connexion à la base de données.');
    }
}

// Ajoute une ligne dans le journal des actions importantes
function ajouterJournal($pdo, $idUtilisateur, $action, $detail)
{
    $requete = $pdo->prepare('INSERT INTO journal (id_utilisateur, action, detail) VALUES (?, ?, ?)');
    $requete->execute([$idUtilisateur, $action, $detail]);
}