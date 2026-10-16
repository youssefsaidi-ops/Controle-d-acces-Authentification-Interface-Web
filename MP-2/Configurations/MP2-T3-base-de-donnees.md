# Base de données — MP2

## 1. Installation de MariaDB

```bash
sudo apt install mariadb-server -y
sudo systemctl status mariadb
```

Résultat obtenu : `active (running)`.

## 2. Sécurisation

```bash
sudo mariadb-secure-installation
```

| Question                                | Réponse |
|:---------------------------------------:|:-------:|
| Switch to unix_socket authentication    | n       |
| Change the root password?               | n       |
| Remove anonymous users?                 | Y       |
| Disallow root login remotely?           | Y       |
| Remove test database and access to it?  | Y       |
| Reload privilege tables now?            | Y       |

Le compte root de MariaDB n'est accessible qu'avec `sudo` sur le serveur.

### Vérifier que la base n'est pas accessible depuis le réseau.

```bash
sudo ss -tlnp | grep 3306
```

Résultat obtenu : `127.0.0.1:3306`. MariaDB n'écoute que sur le serveur lui-même.

## 3. Création de la base et des tables

```bash
sudo mariadb < creation.sql
sudo mariadb -e "SHOW TABLES FROM mp2_salle215;"
```

Résultat obtenu : 

| Tables_in_mp2_salle215 |
|:----------------------:|
| acces                  |
| badges                 |
| qrcodes                |
| reservations           |
| roles                  |
| utilisateurs           |

## 4. Compte de l'application

```sql
CREATE USER 'app_mp2'@'localhost' IDENTIFIED BY '********';
GRANT SELECT, INSERT, UPDATE, DELETE ON mp2_salle215.* TO 'app_mp2'@'localhost';
```

Le mot de passe n'est pas écrit ici pour des raisons de sécurité.

### Tester que le compte ne peut pas supprimer de table.

```sql
DROP TABLE acces;
```

Résultat obtenu : `ERROR 1142 : DROP command denied to user 'app_mp2'@'localhost'`.

## 5. Données de test

```bash
sudo mariadb < donnees_test.sql
```

### Vérifier qu'un nouvel utilisateur est élève par défaut.

```sql
SELECT u.nom, u.prenom, r.nom AS role
FROM utilisateurs u
INNER JOIN roles r ON r.id = u.id_role;
```

Résultat obtenu : 

| Nom     | Prenom | Role  |
|:-------:|:------:|:-----:|
| Martin  | Alice  | admin |
| Bernard | Emma   | eleve |
| Petit   | Hugo   | eleve |
| Dupont  | Lucas  | prof  |

Hugo a été ajouté sans rôle et il est bien élève.

### Afficher l'historique des accès, y compris les badges inconnus.

```sql
SELECT a.date_heure, a.type, a.valeur_lue, u.nom, a.resultat
FROM acces a
LEFT JOIN utilisateurs u ON u.id = a.id_utilisateur;
```

Résultat obtenu : 

| Date_heure          | Type | Valeur_lue   | Nom    | Resultat |
|:-------------------:|:----:|:------------:|:------:|:--------:|
| 2026-10-06 17:17:39 | RFID | A3F2C410     | Martin | autorise |
| 2026-10-06 17:17:39 | RFID | FFFFFFFF     | NULL   | inconnu  |
| 2026-10-06 17:17:39 | RFID | C04D8812     | Petit  | refuse   |
| 2026-10-06 17:17:39 | QR   | qr_test_0001 | Dupont | autorise |

### Tester qu'une réservation qui finit avant de commencer est refusée.

```sql
INSERT INTO reservations (id_utilisateur, date_debut, date_fin)
VALUES (3, '2026-10-12 12:00:00', '2026-10-12 11:00:00');
```

Résultat obtenu : `ERROR 4025 : CONSTRAINT failed`. La réservation est refusée.
