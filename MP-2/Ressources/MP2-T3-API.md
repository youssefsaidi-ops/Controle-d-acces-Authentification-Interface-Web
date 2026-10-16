# Documentation de l'API — MP2

## Informations générales

| Paramètre  | Valeur                       |
|:----------:|:----------------------------:|
| Serveur    | 172.16.10.245                |
| Port       | 80 (http)                    |
| Format     | JSON                         |
| Sécurité   | Clé API dans l'en-tête `X-API-KEY` |

## Choix d'architecture

| Composant          | Accès à la base                | Raison                                                        |
|:------------------:|:------------------------------:|:-------------------------------------------------------------:|
| ESP32 (Tâche 1)    | Par l'API                      | Il est sur un autre appareil, il ne doit jamais accéder directement à la base |
| Site web (Tâche 2) | Directement, en local          | Il est installé sur le même serveur que la base               |

Le site utilise le même compte limité que l'API (`app_mp2`) et le même fichier de configuration sécurisé (`/var/www/config.php`). Les règles de sécurité sont appliquées dans le site : mots de passe hachés, requêtes préparées et contrôle des droits côté serveur.

La clé API est donnée à l'oral, elle n'est jamais écrite sur GitHub.

## 1. Accès par badge RFID

### Objectif

Vérifier si un badge peut ouvrir la salle et enregistrer la tentative.

| Élément   | Valeur                  |
|:---------:|:-----------------------:|
| Adresse   | `/api/rfid.php`         |
| Méthode   | POST                    |
| Utilisé par | Tâche 1 (ESP32)       |

### Données envoyées

```json
{ "uid": "A3F2C410" }
```

L'UID peut être envoyé avec ou sans `:`, en majuscules ou minuscules.

### Réponse

```json
{ "resultat": "autorise", "prenom": "Alice" }
```

| Resultat  | Signification                                                  |
|:---------:|:--------------------------------------------------------------:|
| autorise  | Admin, ou client pendant sa réservation                        |
| inconnu   | Badge pas dans la base                                         |
| refuse    | Badge désactivé, ou client sans réservation en cours           |
| erreur    | Problème côté serveur                                          |

Le prénom n'est envoyé que si l'accès est autorisé.

### Codes HTTP

| Code | Signification                     |
|:----:|:---------------------------------:|
| 200  | Demande traitée                   |
| 400  | UID manquant ou invalide          |
| 401  | Clé API manquante ou invalide     |
| 405  | Méthode autre que POST            |
| 500  | Erreur du serveur                 |
