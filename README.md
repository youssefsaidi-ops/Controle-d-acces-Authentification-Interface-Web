# Réalisation d'un contrôle d'accès avec interface web

## La mission

Le but de cette mission est de créer un contrôle d'accès RFID permettant de trier dynamiquement les personnes autorisées à entrer dans la salle 215.

---

## Équipe

### Groupe 1
- Alban
- Yousef

**Tâche 1 :** Contrôle d’accès RFID

---

### Groupe 2
- Tony
- Nielsen

**Tâche 2 :** QR code, authentification et réservation

---

### Groupe 3
- Grégoire
- Yannis

**Tâche 3 :** Serveur, SGBD, web et intégration

---

## Les tâches

### Objectif Tâche 1
- Installation du module RFID
- Raccordement à l’ESP32
- Lecture de l’UID
- Communication avec le serveur
- Vérification des autorisations
- Gestion des accès refusés
- Journalisation
- Tests de l’intégration

### Objectif Tâche 2
- Interface web
- Génération et validation des QR codes
- Authentification
- Gestion des utilisateurs
- Consultation des disponibilités
- Création des réservations
- Vérification des conflits et des droits
- Tests et documentation

### Objectif Tâche 3
- Serveur Linux
- Serveur web et API
- SGBD
- Gestion des utilisateurs et des droits
- Sécurité
- Intégration du RFID, du QR code et des réservations
- Documentation et procédures

---

## Fonctionnement du GitHub

Dans ce dépôt GitHub, vous trouverez :
- 3 branches correspondant aux 3 tâches
- Le projet avec les *issues*
- 3 *milestones* attribuées aux 3 tâches

---

## Schéma global du fonctionnement du système

### Accès RFID
```mermaid
flowchart TD
    A[Badge présenté] --> B[Lecture de l’UID]
    B --> C[Vérification par le serveur]
    C --> D[Accès autorisé ou refusé]
    D --> E[Journalisation]
