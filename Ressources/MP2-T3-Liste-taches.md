# Tâche 3 — Serveur, base de données, site web et intégration

Binôme : Grégoire Ferouelle, Yannis Hebert

## 1. Préparer le projet

- Faire le schéma du projet avec les tâches 1 et 2 (qui parle à qui)
- Se mettre d'accord avec la tâche 2 sur qui fait la connexion des utilisateurs
- Choisir les logiciels qu'on va utiliser et expliquer pourquoi
- Faire le schéma de la base de données
- Faire la liste des adresses de l'API avec les tâches 1 et 2 (ce qu'on envoie, ce qu'on reçoit)

## 2. Le serveur Linux

- Installer Linux sur le serveur (PC ou machine virtuelle)
- Mettre une adresse IP fixe
- Mettre en place la connexion à distance (SSH) de façon sécurisée
- Créer les comptes utilisateurs du serveur

## 3. La base de données

- Installer la base de données
- Créer les tables (utilisateurs, rôles, badges, réservations, QR codes, historique des accès, état de la salle)
- Créer un compte spécial pour l'application avec peu de droits
- Écrire les fichiers SQL pour tout recréer facilement
- Ajouter des données de test

## 4. Le site web et l'API

- Installer le serveur web
- Faire les adresses de l'API (badge RFID, QR code, réservations, état de la salle)
- Vérifier toutes les données qu'on reçoit
- Expliquer chaque adresse de l'API dans un document

## 5. Les utilisateurs et leurs droits

- Faire les rôles (admin, prof, élève)
- Enregistrer les mots de passe de façon sécurisée (jamais en clair)
- Vérifier les droits à chaque demande

## 6. La sécurité

- Passer le site en HTTPS
- Mettre un pare-feu (ouvrir seulement les ports utiles)
- Protéger l'API utilisée par l'ESP32 avec une clé
- Ne jamais mettre de mot de passe sur GitHub

## 7. Tout relier ensemble

- Relier le lecteur de badge de la tâche 1 au serveur
- Relier le QR code et les réservations de la tâche 2 au serveur
- Tester tout le système du début à la fin

## 8. Surveiller le serveur

- Garder un historique des accès, des erreurs et des connexions
- Installer un outil simple pour voir si le serveur marche bien

## 9. Les tests

- Écrire les fiches de test
- Remplir le tableau des tests (ce qu'on attend, ce qu'on obtient, OK ou pas)

## 10. Le rendu final

- Rapport de notre tâche (10 à 12 pages)
- Notre partie du rapport commun
- Fichier README pour expliquer comment installer
- Préparer la présentation (5-6 min) et la démonstration (2-3 min)

## Matériel

| Matériel                          | Utilisation                         |
|:---------------------------------:|:-----------------------------------:|
| 1 PC (ou machine virtuelle)       | Faire tourner le serveur            |
| Câble réseau                      | Se brancher au réseau de la salle   |
| ESP32 + lecteur de badge + badges | Tests avec la tâche 1               |
| Téléphone ou webcam               | Scanner les QR codes                |

## Logiciels (à choisir ensemble)

| Logiciel                    | Utilisation                  |
|:---------------------------:|:----------------------------:|
| Debian 12 ou Ubuntu Server  | Système du serveur           |
| Apache ou Nginx             | Serveur web                  |
| PHP ou Python               | Langage de l'API             |
| MariaDB                     | Base de données              |
| UFW                         | Pare-feu                     |
| OpenSSL                     | HTTPS                        |
| Postman                     | Tester l'API                 |
| Git, VS Code, draw.io       | Code, versions et schémas    |
