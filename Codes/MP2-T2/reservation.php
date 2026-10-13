        } else {
            $requete = $pdo->prepare('INSERT INTO reservations (id_utilisateur, date_debut, date_fin)
                                      VALUES (?, ?, ?)');
            $requete->execute([$_SESSION['id_utilisateur'], $date_debut, $date_fin]);

            // On récupère l'id de la réservation qu'on vient de créer
            $id_reservation = $pdo->lastInsertId();

            // On génère un code unique pour le QR code
            $code = bin2hex(random_bytes(16));

            $requete = $pdo->prepare('INSERT INTO qrcodes (code, id_reservation) VALUES (?, ?)');
            $requete->execute([$code, $id_reservation]);

            header('Location: mes_reservations.php');
            exit;
        }