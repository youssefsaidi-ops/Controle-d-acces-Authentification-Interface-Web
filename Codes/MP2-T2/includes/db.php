<?php
// Connexion à la base de données avec PDO
require_once __DIR__ . '/../config.php';

function connexion_bdd()
{
    $dsn = 'mysql:host=' . DB_HOTE . ';port=' . DB_PORT . ';dbname=' . DB_NOM . ';charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, DB_UTILISATEUR, DB_MOT_DE_PASSE);
        // En cas d'erreur SQL, PDO lance une exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Les résultats sont des tableaux avec le nom des colonnes
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        // On n'affiche pas le détail de l'erreur à l'utilisateur
        error_log($e->getMessage());
        die('Erreur de connexion à la base de données.');
    }
}