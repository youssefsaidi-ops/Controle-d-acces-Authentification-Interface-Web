# Sécurité du serveur — MP2

## 1. Pare-feu (UFW)

On bloque tout ce qui entre sur le serveur, sauf ce qui est nécessaire.

| Port | Service        | Autorisé depuis              |
|:----:|:--------------:|:----------------------------:|
| 22   | SSH            | Réseau de la salle uniquement |
| 80   | Site et API    | Tout le monde                |
| 443  | HTTPS          | Tout le monde                |

### Installer UFW.

```bash
sudo apt install ufw -y
```

### Règles par défaut.

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
```

### Ouvrir les ports utiles.

```bash
sudo ufw allow from 172.16.10.0/24 to any port 22 proto tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### Vérifier les règles avant d'activer.

```bash
sudo ufw show added
```

La règle du port 22 doit être présente, sinon on perd l'accès SSH.

### Activer.

```bash
sudo ufw enable
sudo ufw status verbose
```

Résultat obtenu : 

| To      | Action   | From            |
|:-------:|:--------:|:---------------:|
| 22/tcp  | ALLOW IN | 172.16.10.0/24  |
| 80/tcp  | ALLOW IN | Anywhere        |
| 443/tcp | ALLOW IN | Anywhere        |

## 2. Tests du pare-feu

| ID  | Test                                   | Résultat attendu   | Résultat obtenu    | Statut |
|:---:|:--------------------------------------:|:------------------:|:------------------:|:------:|
| T10 | Connexion SSH depuis le PC             | Connexion OK       | Connexion OK       | OK     |
| T11 | Site depuis le navigateur              | Page affichée      | Page affichée      | OK     |
| T12 | Port 3306 depuis le PC                 | Bloqué             | Bloqué             | OK     |
| T13 | Trace du blocage dans les journaux     | Ligne UFW BLOCK    | Ligne UFW BLOCK    | OK     |

### Tester un port bloqué depuis le PC (PowerShell).

```powershell
Test-NetConnection 172.16.10.245 -Port 3306
```

Résultat obtenu : `TcpTestSucceeded : False`.

### Retrouver le blocage dans les journaux du serveur.

```bash
sudo journalctl -k | grep "UFW BLOCK" | tail -n 3
```

Résultat obtenu : `[UFW BLOCK] SRC=172.16.10.102 DST=172.16.10.245 DPT=3306`.

## 3. Autres mesures déjà en place

| Mesure                                  | Où                                         |
|:---------------------------------------:|:------------------------------------------:|
| Connexion SSH par clé                   | Installation du serveur                    |
| Connexion root en SSH refusée           | Installation du serveur                    |
| Base de données accessible en local uniquement | Base de données                     |
| Compte de l'application aux droits limités | Base de données                         |
| Mots de passe et clé API hors de GitHub | `/var/www/config.php`, droits 640          |
| Clé API obligatoire pour l'API          | `bdd.php`                                  |
| Requêtes préparées (pas d'injection SQL) | `rfid.php`                                |

## 4. HTTPS

Le serveur n'est pas accessible depuis internet, donc on ne peut pas obtenir un certificat d'une autorité reconnue. On utilise un certificat auto-signé, valable 1 an. Le navigateur affiche un avertissement, mais la connexion est bien chiffrée.

### Activer le module SSL d'Apache.

```bash
sudo a2enmod ssl
```

### Créer le certificat.

```bash
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/srv-mp2.key -out /etc/ssl/certs/srv-mp2.crt -subj "/CN=172.16.10.245" -addext "subjectAltName=IP:172.16.10.245"
```

### Utiliser ce certificat dans Apache.

```bash
sudo sed -i 's#/etc/ssl/certs/ssl-cert-snakeoil.pem#/etc/ssl/certs/srv-mp2.crt#; s#/etc/ssl/private/ssl-cert-snakeoil.key#/etc/ssl/private/srv-mp2.key#' /etc/apache2/sites-available/default-ssl.conf
grep SSLCertificate /etc/apache2/sites-available/default-ssl.conf
```

### Activer le site HTTPS.

```bash
sudo a2ensite default-ssl
sudo apache2ctl configtest
sudo systemctl restart apache2
```

Résultat obtenu : `Syntax OK`.

### Tester.

| ID  | Test                                   | Résultat attendu         | Résultat obtenu          | Statut |
|:---:|:--------------------------------------:|:------------------------:|:------------------------:|:------:|
| T14 | `https://172.16.10.245` dans le navigateur | Page affichée en HTTPS | Page affichée en HTTPS   | OK     |

## 5. Limites

| Limite                                   | Raison                                                                 |
|:----------------------------------------:|:----------------------------------------------------------------------:|
| Certificat auto-signé                    | Serveur pas accessible depuis internet                                 |
| L'ESP32 communique encore en `http`      | Le HTTPS sur ESP32 demande d'intégrer le certificat dans son programme. Les échanges restent sur le réseau de la salle et sont protégés par la clé API |
