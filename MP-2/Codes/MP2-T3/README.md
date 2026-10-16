# Code de la tâche 3 — Serveur, base de données et API

## Contenu

| Fichier | Rôle |
|:-:|:-:|
| creation.sql | Crée la base mp2_salle215 et ses 7 tables |
| donnees_test.sql | Ajoute des données de test |
| config.exemple.php | Modèle du fichier de configuration (sans mot de passe) |
| api/bdd.php | Fonctions communes : connexion, réponse JSON, clé API |
| api/rfid.php | Vérifie un badge et enregistre l'accès |
| api/ping.php | Test réseau pour l'ESP32 |
| scripts/etat-serveur.sh | Affiche l'état du serveur |

## Prérequis

Raspberry Pi OS Lite (Debian 13), Apache, PHP 8.4, MariaDB, UFW.

## Installation

```bash
sudo apt install apache2 php libapache2-mod-php php-mysql mariadb-server ufw -y
sudo mariadb < creation.sql
sudo mariadb < donnees_test.sql
sudo cp -r api /var/www/html/
```

Copier `config.exemple.php` dans `/var/www/config.php`, puis le remplir avec le mot de passe de la base et la clé API.

## Exécution

```bash
curl -X POST http://172.16.10.245/api/rfid.php -H "Content-Type: application/json" -H "X-API-KEY: <clé>" -d '{"uid":"509924D9"}'
```

La procédure complète est dans `MP-2/Configurations/`. Aucun mot de passe ni clé n'est présent dans ce dépôt.
