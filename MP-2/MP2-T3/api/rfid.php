<?php
require __DIR__ . '/bdd.php';

// Seule la méthode POST est acceptée
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    repondre(405, ['erreur' => 'Méthode non autorisée, utilisez POST']);
}

verifierCleApi();

// Lecture de l'UID envoyé en JSON
$donnees = json_decode(file_get_contents('php://input'), true);
$uid = $donnees['uid'] ?? '';

// Mise en forme : majuscules, sans espace, ":" ni "-"
$uid = strtoupper(str_replace([':', ' ', '-'], '', $uid));

// L'UID doit contenir seulement des caractères hexadécimaux
if (!preg_match('/^[0-9A-F]{4,20}$/', $uid)) {
    repondre(400, ['erreur' => 'UID manquant ou invalide']);
}

try {
    $bdd = connexionBdd();

    // On cherche le badge et son propriétaire
    $requete = $bdd->prepare(
        'SELECT u.id, u.prenom, u.id_role, b.actif
         FROM badges b
         INNER JOIN utilisateurs u ON u.id = b.id_utilisateur
         WHERE b.uid = ?'
    );
    $requete->execute([$uid]);
    $badge = $requete->fetch();

    if (!$badge) {
        // Badge pas dans la base
        $resultat = 'inconnu';
        $idUtilisateur = null;
    } else {
        $idUtilisateur = $badge['id'];

        if (!$badge['actif']) {
            // Badge désactivé
            $resultat = 'refuse';
        } elseif ($badge['id_role'] == 1) {
            // Admin : accès autorisé tout le temps
            $resultat = 'autorise';
        } else {
            // Client : accès autorisé seulement pendant une réservation
            $requete = $bdd->prepare(
                'SELECT COUNT(*) FROM reservations
                 WHERE id_utilisateur = ? AND NOW() BETWEEN date_debut AND date_fin'
            );
            $requete->execute([$idUtilisateur]);
            $resultat = $requete->fetchColumn() > 0 ? 'autorise' : 'refuse';
        }
    }

    // On enregistre chaque tentative dans l'historique
    $requete = $bdd->prepare(
        'INSERT INTO acces (type, valeur_lue, id_utilisateur, resultat) VALUES (?, ?, ?, ?)'
    );
    $requete->execute(['RFID', $uid, $idUtilisateur, $resultat]);

    // Le prénom n'est envoyé que si l'accès est autorisé
    $reponse = ['resultat' => $resultat];
    if ($resultat === 'autorise') {
        $reponse['prenom'] = $badge['prenom'];
    }
    repondre(200, $reponse);

} catch (Exception $e) {
    // L'erreur détaillée va dans les journaux du serveur, pas dans la réponse
    error_log('rfid.php : ' . $e->getMessage());
    repondre(500, ['resultat' => 'erreur']);
}
