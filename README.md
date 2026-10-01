# Contrôle d'accès RFID - Salle 215

Mini-projet 2, tâche 1 : authentification par badge RFID avec l'ESP32.
Réalisé par Alban et Youssef.

## Objectif

On utilise un lecteur RFID RC522 branché sur un ESP32 pour contrôler l'accès
à la salle 215. Quand on passe un badge, l'ESP32 lit son UID et l'envoie à
l'API du serveur Linux. Selon la réponse, la porte s'ouvre ou non.

## Branchement

Le RC522 est relié à l'ESP32 par le bus SPI :

| RC522 | ESP32   |
|-------|---------|
| SDA   | GPIO 5  |
| SCK   | GPIO 18 |
| MOSI  | GPIO 23 |
| MISO  | GPIO 19 |
| RST   | GPIO 22 |
| 3.3V  | 3V3     |
| GND   | GND     |

Attention : le RC522 s'alimente en 3.3V, pas en 5V.

Sorties :
- GPIO 26 : relais (gâche) + LED verte
- GPIO 27 : LED rouge + buzzer

## Fonctionnement du programme

1. L'ESP32 attend qu'un badge soit présenté et lit son UID.
2. Il envoie l'UID à l'API avec une requête POST (JSON).
3. Il attend la réponse du serveur et réagit en fonction du code reçu.

## Cas possibles

| Situation | Réponse de l'API | Ce que fait l'ESP32 |
|-----------|------------------|---------------------|
| Badge autorisé | 200, `granted` | Relais activé 3 s, LED verte, 1 bip court |
| Badge inconnu | 403, `unknown` | LED rouge, 2 bips longs |
| Badge connu mais refusé | 403, `denied` | LED rouge (droits insuffisants ou hors horaire) |
| Wi-Fi coupé ou serveur HS | erreur 5xx ou timeout | LED rouge qui clignote, message dans le moniteur série |

## Échange avec l'API

Requête envoyée par l'ESP32 :

- URL : `POST /api/v1/access/rfid`
- Header : `Content-Type: application/json`

    {
      "uid": "A1B2C3D4",
      "reader_id": "ESP32_DOOR_215",
      "timestamp": "2026-10-01T10:40:00Z"
    }
