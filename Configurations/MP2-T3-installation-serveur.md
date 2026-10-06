# Installation du serveur Linux — MP2

## 1. Matériel et système

| Élément       | Choix                            |
|:-------------:|:--------------------------------:|
| Machine       | Raspberry Pi                     |
| Système       | Raspberry Pi OS Lite (64-bit)    |
| Base          | Debian 13                        |
| Nom d'hôte    | srv-mp2                          |
| Connexion     | Câble réseau (pas de Wi-Fi)      |

On a choisi la version Lite parce qu'un serveur n'a pas besoin d'interface graphique. Ça laisse plus de ressources pour le reste.

## 2. Préparation de la carte SD

### Graver le système sur la carte SD avec Raspberry Pi Imager.

* Appareil : notre modèle de Raspberry Pi
* Système : Raspberry Pi OS (other) → Raspberry Pi OS Lite (64-bit)
* Stockage : la carte microSD

## 3. Premier démarrage

### Créer l'utilisateur au premier démarrage (écran et clavier branchés).

Le système demande le clavier, le nom d'utilisateur et le mot de passe.

### Activer SSH et changer le nom d'hôte.

```bash
sudo raspi-config
```

* Interface Options → SSH → Yes
* System Options → Hostname → srv-mp2

### Récupérer l'adresse IP.

```bash
hostname -I
```

Résultat obtenu : 

| Adresse IP     |
|:--------------:|
| 172.16.10.225  |

L'adresse est donnée automatiquement par le réseau pour l'instant. Elle sera rendue fixe plus tard.

## 4. Mise à jour du système

```bash
sudo apt update && sudo apt full-upgrade -y
sudo reboot
```

Résultat obtenu : 14 paquets mis à jour, aucune erreur.

## 5. Fuseau horaire

```bash
sudo timedatectl set-timezone Europe/Paris
timedatectl
```

Résultat obtenu : 

| Paramètre                  | Valeur                     |
|:--------------------------:|:--------------------------:|
| Time zone                  | Europe/Paris (CEST, +0200) |
| System clock synchronized  | yes                        |
| NTP service                | active                     |

## 6. Connexion SSH par clé

### Créer la clé sur le PC (PowerShell).

```powershell
ssh-keygen -t ed25519
```

### Envoyer la clé publique sur le serveur.

```powershell
type $env:USERPROFILE\.ssh\id_ed25519.pub | ssh gregoire@172.16.10.225 "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
```

### Tester la connexion.

```powershell
ssh gregoire@172.16.10.225
```

Résultat obtenu : le serveur demande la passphrase de la clé, et plus le mot de passe du compte.

## 7. Vérification de l'accès root

```powershell
ssh root@172.16.10.225
```

Résultat obtenu : `Permission denied`. La connexion en root est bien refusée.

## 8. Compte de Yannis

```bash
sudo adduser yannis
sudo usermod -aG sudo yannis
groups yannis
```

Résultat obtenu : `yannis` fait partie du groupe `sudo`.

## Reste à faire

* Mettre une adresse IP fixe (en attente de l'adresse donnée par le prof)
