# Fiche-recette — Accès par badge avec réservation

## 1. Objectif

Vérifier qu'un client n'a accès à la salle 215 que pendant une réservation faite sur le site, et que chaque passage de badge est enregistré.

## 2. Prérequis

* Serveur `srv-mp2` allumé et joignable (`172.16.10.245`)
* ESP32 de la tâche 1 connecté au Wi-Fi `Wifi_Iot215`
* Compte client `emma.bernard@test.fr` associé au badge `FA488D2E`
* Aucune réservation en cours pour Emma

## 3. Matériel et logiciels

| Élément                         | Rôle                                  |
|:-------------------------------:|:-------------------------------------:|
| Raspberry Pi (Apache, PHP, MariaDB) | Serveur, API et base de données   |
| ESP32 + lecteur RFID MFRC522    | Lecture du badge                      |
| Badge `FA488D2E`                | Badge d'Emma (client)                 |
| PC avec navigateur              | Réservation sur le site               |
| PC avec PowerShell (SSH)        | Lecture de l'historique des accès     |

## 4. Conditions initiales

```sql
SELECT id, date_debut, date_fin FROM reservations
WHERE id_utilisateur = 3 AND date_fin > NOW();
```

Résultat attendu : aucune ligne.

## 5. Étapes

| N° | Action                                                         | Résultat attendu                                  |
|:--:|:--------------------------------------------------------------:|:-------------------------------------------------:|
| 1  | Passer le badge `FA488D2E`                                     | LED rouge, accès `refuse` enregistré              |
| 2  | Se connecter au site en Emma                                   | Page d'accueil client                             |
| 3  | Réserver de maintenant à dans 15 minutes                       | Réservation enregistrée avec un QR code           |
| 4  | Réserver un créneau qui chevauche le premier                   | Message « Ce créneau est déjà réservé. »          |
| 5  | Repasser le badge `FA488D2E`                                   | LED verte, accès `autorise` enregistré            |
| 6  | Annuler la réservation dans « Mes réservations »              | Ligne `annulation_reservation` dans le journal    |
| 7  | Repasser le badge `FA488D2E`                                   | LED rouge, accès `refuse` enregistré              |

### Vérifier l'historique des accès.

```sql
SELECT a.date_heure, a.valeur_lue, u.prenom, a.resultat
FROM acces a
LEFT JOIN utilisateurs u ON u.id = a.id_utilisateur
ORDER BY a.id DESC LIMIT 5;
```

## 6. Résultat obtenu (15/10/2026)

| Heure    | Badge    | Prénom | Résultat |
|:--------:|:--------:|:------:|:--------:|
| 10:53:20 | FA488D2E | Emma   | refuse   |
| 11:06:05 | FA488D2E | Emma   | autorise |
| 11:07:03 | FA488D2E | Emma   | refuse   |

| Étape | Statut |
|:-----:|:------:|
| 1     | OK     |
| 2     | OK     |
| 3     | OK     |
| 4     | OK     |
| 5     | OK     |
| 6     | OK     |
| 7     | OK     |

## 7. Observations

* Chaque passage de badge est enregistré deux fois dans la même seconde : le lecteur lit la carte deux fois. Correction à faire côté ESP32 (ignorer le même UID pendant 2 secondes).
* La réservation et l'annulation sont prises en compte immédiatement par le badge.
