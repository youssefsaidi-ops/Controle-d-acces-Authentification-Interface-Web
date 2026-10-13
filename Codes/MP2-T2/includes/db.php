<?php
// Connexion à la base de données avec PDO

if (file_exists('/var/www/config.php')) {
    // Sur le serveur de T3 : les identifiants de la base sont dans ce fichier sécurisé
    require_once '/var/www/config.php';

    if (!defined('SITE_URL')) {
        define('SITE_URL', 'https://172.16.10.245');
    }
} else {
    // En local, sur notre PC (XAMPP) : notre propre fichier de config
    require_once 'config.php';
}

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