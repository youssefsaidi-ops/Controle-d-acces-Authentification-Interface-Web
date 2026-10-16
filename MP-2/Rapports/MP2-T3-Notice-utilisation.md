# Notice d'utilisation — Serveur du MP2

## 1. Prérequis

| Élément              | Valeur                                   |
|:--------------------:|:----------------------------------------:|
| Serveur              | Raspberry Pi `srv-mp2`                   |
| Adresse IP           | 172.16.10.245                            |
| Système              | Raspberry Pi OS Lite (Debian 13)         |
| Services             | Apache, PHP 8.4, MariaDB, UFW            |
| Accès administrateur | SSH par clé, depuis le réseau de la salle |

Le mot de passe de la base et la clé API sont dans `/var/www/config.php`. Ils ne sont jamais écrits sur GitHub.

## 2. Démarrer le serveur

* Brancher le câble réseau, puis l'alimentation en dernier.
* Attendre 1 à 2 minutes.

### Se connecter depuis un PC (PowerShell).

```powershell
ssh gregoire@172.16.10.245
```

### Vérifier que tout fonctionne.

```bash
sudo etat-serveur
```

Résultat attendu : `apache2 : OK`, `mariadb : OK`, `pare-feu : OK`.

## 3. Utiliser le système

| Qui              | Comment                                                       |
|:----------------:|:-------------------------------------------------------------:|
| Utilisateur      | Site : `https://172.16.10.245` (créer un compte, réserver)    |
| Administrateur   | Même site, menu de gestion des utilisateurs                   |
| ESP32 (badges)   | API : `http://172.16.10.245/api/rfid.php` (POST, clé API)     |
| Test réseau ESP32 | `http://172.16.10.245/api/ping.php` (GET)                    |

Un administrateur est toujours autorisé. Un client n'est autorisé que pendant une de ses réservations.

## 4. Consulter les logs

### Passages de badge.

```bash
sudo mariadb mp2_salle215 -e "SELECT a.date_heure, a.valeur_lue, u.prenom, a.resultat FROM acces a LEFT JOIN utilisateurs u ON u.id = a.id_utilisateur ORDER BY a.id DESC LIMIT 10;"
```

### Actions du site.

```bash
sudo mariadb mp2_salle215 -e "SELECT date_heure, action, detail FROM journal ORDER BY id DESC LIMIT 10;"
```

### Erreurs.

```bash
sudo tail -n 10 /var/log/apache2/error.log
```

## 5. Ajouter un badge

```bash
sudo mariadb mp2_salle215 -e "INSERT INTO badges (uid, id_utilisateur, actif) VALUES ('UID_DU_BADGE', ID_UTILISATEUR, TRUE);"
```

L'UID s'écrit en majuscules, sans espace ni `:` (ex : `509924D9`).

## 6. Arrêter le serveur

```bash
sudo shutdown now
```

Attendre que la lumière verte arrête de clignoter avant de débrancher. Ne jamais débrancher directement : la carte SD peut s'abîmer.

## 7. Problèmes courants

| Problème                                   | Cause probable                                  | Solution                                                      |
|:------------------------------------------:|:-----------------------------------------------:|:-------------------------------------------------------------:|
| Impossible de se connecter en SSH          | Serveur éteint ou câble débranché               | Vérifier les lumières du Raspberry et le câble                |
| L'API répond 500 (« erreur »)              | Mauvais mot de passe dans `config.php`          | Lire `/var/log/apache2/error.log`                             |
| L'API répond 401                           | Clé API manquante ou fausse                     | Vérifier la clé dans le code de l'ESP32                       |
| L'ESP32 ne joint pas le serveur            | Mauvaise adresse dans son code                  | API : `/api/rfid.php`, test : `/api/ping.php`                 |
| Un badge connu est refusé                  | Client sans réservation en cours, ou badge inactif | Vérifier les réservations et la colonne `actif`            |
| Le navigateur affiche « Not secure »       | Certificat auto-signé                           | Normal : la connexion est quand même chiffrée                 |
| Un service affiche `ARRÊTÉ`                | Service planté                                  | `sudo systemctl restart apache2` (ou `mariadb`)               |
