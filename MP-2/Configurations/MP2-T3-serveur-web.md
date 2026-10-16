# Serveur web et API — MP2

## 1. Installation d'Apache et PHP

```bash
sudo apt install apache2 php libapache2-mod-php php-mysql -y
sudo systemctl status apache2
php -v
```

Résultat obtenu : Apache `active (running)`, PHP 8.4.26.

### Vérifier Apache et PHP depuis un PC du réseau.

* `http://172.16.10.245` : page « Apache2 Debian Default Page »
* `http://172.16.10.245/test.php` : « PHP fonctionne » (fichier supprimé après le test)

## 2. Fichier de configuration

Le mot de passe de la base et la clé API sont dans `/var/www/config.php`, en dehors du dossier du site. Ce fichier n'est jamais mis sur GitHub (modèle : `Codes/MP2-T3/config.exemple.php`).

### Générer la clé API.

```bash
openssl rand -hex 16
```

### Protéger le fichier.

```bash
sudo chown root:www-data /var/www/config.php
sudo chmod 640 /var/www/config.php
```

Résultat obtenu : `-rw-r----- root www-data`. Seuls l'administrateur et Apache peuvent le lire.

## 3. Mise en ligne de l'API

```bash
sudo mkdir -p /var/www/html/api
cd /var/www/html/api
sudo curl -O https://raw.githubusercontent.com/youssefsaidi-ops/Controle-d-acces-Authentification-Interface-Web/t3-serveur-web/Codes/MP2-T3/api/bdd.php
sudo curl -O https://raw.githubusercontent.com/youssefsaidi-ops/Controle-d-acces-Authentification-Interface-Web/t3-serveur-web/Codes/MP2-T3/api/rfid.php
```

## 4. Tests de rfid.php

| ID  | Test                               | Résultat attendu          | Résultat obtenu           | Statut |
|:---:|:----------------------------------:|:-------------------------:|:-------------------------:|:------:|
| T01 | Badge admin                        | autorise, Alice, 200      | autorise, Alice, 200      | OK     |
| T02 | Même badge écrit `a3:f2:c4:10`     | autorise, Alice, 200      | autorise, Alice, 200      | OK     |
| T03 | Badge inconnu                      | inconnu, 200              | inconnu, 200              | OK     |
| T04 | Badge désactivé                    | refuse, 200               | refuse, 200               | OK     |
| T05 | Client sans réservation en cours   | refuse, 200               | refuse, 200               | OK     |
| T06 | Sans clé API                       | 401                       | 401                       | OK     |
| T07 | UID invalide                       | 400                       | 400                       | OK     |
| T08 | Méthode GET                        | 405                       | 405                       | OK     |
| T09 | Client pendant sa réservation      | autorise, Emma, 200       | autorise, Emma, 200       | OK     |

### Exemple de commande de test.

```bash
curl -s -w "\n%{http_code}\n" -X POST http://172.16.10.245/api/rfid.php -H "Content-Type: application/json" -H "X-API-KEY: $CLE" -d '{"uid":"A3F2C410"}'
```

### Vérifier l'historique des accès.

```sql
SELECT a.date_heure, a.valeur_lue, u.prenom, a.resultat
FROM acces a
LEFT JOIN utilisateurs u ON u.id = a.id_utilisateur
ORDER BY a.id DESC LIMIT 8;
```

Résultat obtenu : chaque tentative est enregistrée. L'UID `a3:f2:c4:10` est enregistré `A3F2C410`. Les tests T06 à T08 ne sont pas enregistrés car la requête est rejetée avant d'arriver à la base.

## 5. Anomalie rencontrée

| Problème                                  | Cause                                         | Correction                          |
|:-----------------------------------------:|:---------------------------------------------:|:-----------------------------------:|
| Code 500 au premier test                  | Mauvais mot de passe dans `config.php`        | Mot de passe corrigé                |

L'erreur a été trouvée dans `/var/log/apache2/error.log` : `Access denied for user 'app_mp2'`.
