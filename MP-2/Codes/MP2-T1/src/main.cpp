#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <WebServer.h>
#include <HTTPClient.h>

// Identifiants Wi-Fi
const char* SSID_WIFI = "Wifi_Iot215";
const char* PASS_WIFI = "CIEL1234#+";

// Configuration Serveur Raspberry Pi & API
const char* RASPBERRY_IP        = "172.16.10.245";
const char* API_KEY             = "acaa2c160d087f00d94d77393a0185f7"; // Clé reçue
const char* RASPBERRY_URL_TEST  = "http://172.16.10.245/";
const char* RASPBERRY_URL_API   = "http://172.16.10.245/api/rfid.php";

// Pins RFID & LEDs
#define SS_PIN         5
#define RST_PIN        22
#define LED_VERTE_PIN  26
#define LED_ROUGE_PIN  27

MFRC522 rfid(SS_PIN, RST_PIN);
WebServer server(80);

// Variables d'état
String dernierBadge = "Aucun";
String dernierStatut = "En attente...";
String dernierUtilisateur = "";
int nbAccesLiaison = 0;

// Minuterie non-bloquante pour les LED
unsigned long tempsEteindreLed = 0;

// Génération du tableau de bord HTML
String genererHTML() {
  String html = "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  html += "<meta http-equiv='refresh' content='2'>";
  html += "<title>Contrôle d'accès ESP32</title>";
  html += "<style>";
  html += "body { font-family: Arial, sans-serif; background: #f2f2f2; color: #111; margin: 0; padding: 20px; }";
  html += ".container { max-width: 380px; margin: 50px auto; background: #fff; border: 1px solid #ccc; border-radius: 8px; padding: 20px; }";
  html += "h1 { font-size: 18px; text-align: center; margin-bottom: 18px; }";
  html += ".badge-box, .user-box, .status, .counter { border: 1px solid #ddd; background: #fafafa; padding: 10px; margin-bottom: 12px; border-radius: 5px; }";
  html += ".badge-box { font-family: monospace; font-weight: bold; }";
  html += ".status { text-align: center; font-weight: bold; }";
  html += ".ok { background: #eaf8ea; color: #136a13; border-color: #b7d9b7; }";
  html += ".fail { background: #fdecec; color: #8d1f1f; border-color: #e4b5b5; }";
  html += ".wait { background: #f4f4f4; color: #444; border-color: #d7d7d7; }";
  html += ".counter { text-align: center; }";
  html += "</style></head><body>";
  
  html += "<div class='container'>";
  html += "<h1>Contrôle d'Accès Salle 215</h1>";
  html += "<div class='badge-box'>Badge : " + dernierBadge + "</div>";
  
  if (dernierUtilisateur != "") {
    html += "<div class='user-box'>Utilisateur : " + dernierUtilisateur + "</div>";
  }

  if (dernierStatut == "AUTORISÉ") {
    html += "<div class='status ok'>Accès Accordé</div>";
  } else if (dernierStatut == "REFUSÉ" || dernierStatut == "INCONNU" || dernierStatut == "ERREUR") {
    html += "<div class='status fail'>" + dernierStatut + "</div>";
  } else {
    html += "<div class='status wait'>En attente...</div>";
  }
  
  html += "<div class='counter'>Entrées autorisées : <span>" + String(nbAccesLiaison) + "</span></div>";
  html += "</div></body></html>";
  return html;
}

void handleRoot() {
  server.send(200, "text/html", genererHTML());
}

// 1. Test réseau initial (HTTP GET)
bool testerConnexionRaspberry() {
  if (WiFi.status() != WL_CONNECTED) return false;

  HTTPClient http;
  http.begin(RASPBERRY_URL_TEST);
  http.setTimeout(2000); // Timeout de 2s

  int httpCode = http.GET();
  http.end();

  Serial.print("Test réseau HTTP GET racine (172.16.10.245) : ");
  Serial.println(httpCode);

  return (httpCode == HTTP_CODE_OK);
}

// 2. Envoi du badge à l'API (HTTP POST)
String envoyerRequeteRaspberry(String uid) {
  if (WiFi.status() != WL_CONNECTED) return "ERREUR_WIFI";

  HTTPClient http;
  http.begin(RASPBERRY_URL_API);
  http.setTimeout(3000); // Timeout de 3s
  
  // En-têtes exigés par l'API
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-KEY", API_KEY);
  http.addHeader("User-Agent", "ESP32-RFID");
  http.addHeader("Connection", "close");

  String body = "{\"uid\":\"" + uid + "\"}";
  int httpCode = http.POST(body);
  String reponseServeur = "";

  if (httpCode == HTTP_CODE_OK) {
    reponseServeur = http.getString();
    Serial.println("Réponse API RPi : " + reponseServeur);
  } else {
    Serial.print("Erreur HTTP POST (Code ");
    Serial.print(httpCode);
    Serial.println(")");
    reponseServeur = "ERREUR";
  }

  http.end();
  return reponseServeur;
}

void setup() {
  Serial.begin(115200);

  pinMode(LED_VERTE_PIN, OUTPUT);
  pinMode(LED_ROUGE_PIN, OUTPUT);
  digitalWrite(LED_VERTE_PIN, LOW);
  digitalWrite(LED_ROUGE_PIN, LOW);

  SPI.begin(18, 19, 23, SS_PIN);
  rfid.PCD_Init();

  // Connexion Wi-Fi
  WiFi.begin(SSID_WIFI, PASS_WIFI);
  Serial.print("Connexion au Wi-Fi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWi-Fi Connecté !");
  Serial.print("Serveur Web ESP32 sur : http://");
  Serial.println(WiFi.localIP());

  // Test de présence du serveur RPi
  if (testerConnexionRaspberry()) {
    Serial.println(">> RÉSEAU OK : Le serveur 172.16.10.245 réponds bien !");
  } else {
    Serial.println(">> AVERTISSEMENT : Impossible de joindre la racine du serveur.");
  }

  // Démarrage du serveur web embarqué
  server.on("/", handleRoot);
  server.begin();
}

void loop() {
  // Traiter les clients web de l'ESP32
  server.handleClient();

  // Extinction automatique des LED (gestion non-bloquante)
  if (tempsEteindreLed > 0 && millis() > tempsEteindreLed) {
    digitalWrite(LED_VERTE_PIN, LOW);
    digitalWrite(LED_ROUGE_PIN, LOW);
    tempsEteindreLed = 0;
  }

  // Lecture RFID
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    delay(10);
    return;
  }

  // Formatage de l'UID
  String cardUID = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) cardUID += "0";
    cardUID += String(rfid.uid.uidByte[i], HEX);
  }
  cardUID.toUpperCase();
  dernierBadge = cardUID;

  Serial.println("\n----------------------------------");
  Serial.println("Scan du badge : " + cardUID);

  // Requête API
  String reponseJSON = envoyerRequeteRaspberry(cardUID);

  // Traitement du résultat
  if (reponseJSON.indexOf("\"autorise\"") > -1) {
    dernierStatut = "AUTORISÉ";
    nbAccesLiaison++;

    int posPrenom = reponseJSON.indexOf("\"prenom\":");
    if (posPrenom > -1) {
      int debut = reponseJSON.indexOf("\"", posPrenom + 9) + 1;
      int fin = reponseJSON.indexOf("\"", debut);
      dernierUtilisateur = reponseJSON.substring(debut, fin);
    } else {
      dernierUtilisateur = "Client";
    }

    digitalWrite(LED_VERTE_PIN, HIGH);
    digitalWrite(LED_ROUGE_PIN, LOW);
  } 
  else if (reponseJSON.indexOf("\"refuse\"") > -1) {
    dernierStatut = "REFUSÉ";
    dernierUtilisateur = "Accès non autorisé";
    digitalWrite(LED_ROUGE_PIN, HIGH);
    digitalWrite(LED_VERTE_PIN, LOW);
  } 
  else if (reponseJSON.indexOf("\"inconnu\"") > -1) {
    dernierStatut = "INCONNU";
    dernierUtilisateur = "Badge non enregistré";
    digitalWrite(LED_ROUGE_PIN, HIGH);
    digitalWrite(LED_VERTE_PIN, LOW);
  } 
  else {
    dernierStatut = "ERREUR";
    dernierUtilisateur = "Erreur serveur / API";
    digitalWrite(LED_ROUGE_PIN, HIGH);
    digitalWrite(LED_VERTE_PIN, LOW);
  }

  // Maintien des LED allumées pendant 2 secondes
  tempsEteindreLed = millis() + 2000;

  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}
