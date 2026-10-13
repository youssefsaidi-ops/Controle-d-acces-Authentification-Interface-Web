# Schéma de la base de données — MP2

## Schéma

<img width="678" height="917" alt="SchémaBDD" src="https://github.com/user-attachments/assets/b0236649-998d-4955-b142-216954cf15ac" />

## Les tables

| Table        | Rôle                                                       |
|:------------:|:----------------------------------------------------------:|
| roles        | Les rôles possibles : admin, client                        |
| utilisateurs | Les comptes. Rôle client par défaut                        |
| badges       | Les badges RFID, chacun lié à un utilisateur               |
| reservations | Les réservations de la salle 215                           |
| qrcodes      | Un QR code généré pour chaque réservation                  |
| acces        | L'historique de toutes les tentatives d'accès (RFID et QR) |

## Choix

### Deux rôles : admin et client

Tout nouveau compte est client par défaut. Seul un admin peut donner le rôle admin.

### Règle d'accès

Un admin peut entrer à tout moment. Un client peut entrer seulement pendant une de ses réservations.

### Pas de table pour l'état de la salle

L'état (libre, réservée, occupée) est calculé à partir des réservations et des accès. On évite ainsi d'avoir une information qui ne correspond plus à la réalité.

### Résultat d'un accès

La colonne `resultat` est de type `ENUM` et ne peut valoir que : `autorise`, `inconnu`, `refuse` ou `erreur`.

La colonne `type` est aussi de type `ENUM` et ne peut valoir que : `RFID` ou `QR`.

### Badge inconnu

Un accès peut ne pas avoir d'utilisateur (`id_utilisateur` vide) quand le badge n'existe pas dans la base.

### UID des badges

L'UID est stocké en majuscules, sans espace ni `:` (ex : `A3F2C410`).
