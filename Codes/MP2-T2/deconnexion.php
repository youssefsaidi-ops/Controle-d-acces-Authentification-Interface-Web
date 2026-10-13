<?php
// Déconnexion : on détruit la session puis on retourne à la connexion
session_start();
session_destroy();

header('Location: connexion.php');
exit;