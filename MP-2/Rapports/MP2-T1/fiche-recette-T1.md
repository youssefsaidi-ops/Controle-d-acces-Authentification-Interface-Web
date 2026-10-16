# Cahier de Recette et Fiches de Validation du Projet

**Projet :** Mini-Projet 2 — Système de contrôle d'accès et de réservation (Salle 215)  
**Tâche :** Tâche 1 — Contrôle d'accès RFID  
**Responsables :** Youssef S. & Alban P.  
**Formation :** BTS CIEL IR — Carnus Rodez  
**Date :** Octobre 2026  

---

## 1. Informations Générales de Recette

- **Équipement sous test :** Carte ESP32 DevKit v1 + Lecteur RFID NXP MFRC522 + LEDs (Verte/Rouge) + Buzzer
- **Environnement réseau :** Point d'accès Wi-Fi (`Wifi_Salle_215`)
- **Serveur central :** Serveur Linux API REST / BDD SQL (`172.16.10.245`)
- **Version du Firmware :** `v1.0.0`

---

## 2. Matrice Globale des Tests de Recette

| ID Test | Fonctionnalité | Objectif du test | Conditions initiales | Procédure / Actions | Résultat attendu | Statut |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: |
| **REC-01** | **Initialisation Matérielle** | Vérifier le démarrage de l'ESP32 et du bus SPI | ESP32 sous tension via câble USB | Démarrer l'ESP32 et observer le moniteur série (115200 bauds) | Message de bienvenue et initialisation du bus SPI / MFRC522 sans erreur | **OK** |
| **REC-02** | **Connexion Réseau Wi-Fi** | Valider la connexion Wi-Fi de l'ESP32 | Point d'accès Wi-Fi actif | Attendre la procédure d'association Wi-Fi dans le `setup()` | Attribution d'une adresse IP valide confirmée sur le port série | **OK** |
| **REC-03** | **Lecture UID RFID** | S'assurer de la détection et de la lecture sans contact | Badge MIFARE 13.56 MHz disponible | Approcher le badge à moins de 2 cm du lecteur MFRC522 | L'UID (ex: `A1B2C3D4`) est lu et affiché sur la console série | **OK** |
| **REC-04** | **Accès Autorisé (Nominal)** | Valider le déclenchement d'un accès accordé | Badge enregistré en BDD avec réservation active | Scanner le badge autorisé devant le lecteur RFID | Requête POST envoyée à l'API (`172.16.10.245`), code HTTP `200` reçu, LED Verte + Buzzer activés 2s | **OK** |
| **REC-05** | **Badge Inconnu (Refus)** | Valider le rejet d'un badge non enregistré | Badge non répertorié dans la BDD | Scanner le badge inconnu devant le lecteur RFID | Requête POST envoyée, code HTTP `403` reçu, LED Rouge allumée 2s | **OK** |
| **REC-06** | **Non Autorisé (Créneau)** | Valider le rejet d'un utilisateur hors créneau | Badge connu mais sans réservation active | Scanner le badge devant le lecteur RFID | Code HTTP `403` reçu, accès refusé et événement journalisé en BDD | **OK** |
| **REC-07** | **Journalisation BDD** | Vérifier l'enregistrement des événements | BDD SQL active sur le serveur Linux | Effectuer un scan puis vérifier la table `logs_acces` | Création d'une nouvelle ligne en BDD (date/heure, UID, statut) | **OK** |
| **REC-08** | **Pannes Réseau Wi-Fi** | Vérifier la réaction en cas de perte réseau | Point d'accès Wi-Fi éteint | Scanner un badge lorsque le Wi-Fi est indisponible | Signal d'erreur local et tentatives automatiques de reconnexion | **OK** |

---

## 3. Fiches-Recettes Détaillées

### Fiche de Test : REC-04 (Accès Autorisé)

- **Objectif :** Valider l'envoi de la requête JSON, le traitement par l'API REST et la rétroaction lumineuse/sonore d'autorisation.
- **Prérequis :** ESP32 connecté au Wi-Fi, serveur `172.16.10.245` opérationnel, badge enregistré en BDD avec droit actif.
- **Étapes de réalisation :**
  1. Présenter le badge autorisé devant le lecteur RC522.
  2. Observer l'envoi de la payload JSON `{ "uid": "A1B2C3D4" }`.
  3. Vérifier la réponse HTTP renvoyée par l'API REST.
- **Résultat attendu :** Réponse HTTP `200 OK`, LED Verte allumée pendant 2 secondes, signal sonore court du buzzer.
- **Résultat obtenu :** Conforme au comportement attendu. Le log apparaît sur la console série et dans la table SQL.
- **Statut :** **VALIDE (OK)**

---

### Fiche de Test : REC-05 (Badge Inconnu)

- **Objectif :** S'assurer qu'un badge non enregistré ne peut pas déverrouiller l'accès.
- **Prérequis :** Système sous tension, badge vierge ou non inscrit en BDD.
- **Étapes de réalisation :**
  1. Présenter le badge inconnu devant le lecteur.
  2. Vérifier la réponse renvoyée par le serveur Linux.
- **Résultat attendu :** Réponse HTTP `403 Forbidden`, LED Verte éteinte, LED Rouge allumée pendant 2 secondes.
- **Résultat obtenu :** Conforme. L'accès est refusé et la tentative est enregistrée comme "Inconnue" en base de données.
- **Statut :** **VALIDE (OK)**