-- Création de la base de données du MP2 (salle 215)
-- ATTENTION : ce script efface la base existante et la recrée

DROP DATABASE IF EXISTS mp2_salle215;

CREATE DATABASE mp2_salle215
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE mp2_salle215;

CREATE TABLE roles (
  id  INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO roles (id, nom) VALUES
  (1, 'admin'),
  (2, 'client');

CREATE TABLE utilisateurs (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(50)  NOT NULL,
  prenom        VARCHAR(50)  NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe  VARCHAR(255) NOT NULL,
  id_role       INT NOT NULL DEFAULT 2,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_role) REFERENCES roles(id)
);

CREATE TABLE badges (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  uid            VARCHAR(20) NOT NULL UNIQUE,
  id_utilisateur INT NOT NULL,
  actif          BOOLEAN NOT NULL DEFAULT TRUE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE reservations (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL,
  date_debut     DATETIME NOT NULL,
  date_fin       DATETIME NOT NULL,
  date_creation  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  CHECK (date_fin > date_debut)
);

CREATE TABLE qrcodes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  code           VARCHAR(64) NOT NULL UNIQUE,
  id_reservation INT NOT NULL UNIQUE,
  date_creation  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_reservation) REFERENCES reservations(id) ON DELETE CASCADE
);

CREATE TABLE acces (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  date_heure     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type           ENUM('RFID', 'QR') NOT NULL,
  valeur_lue     VARCHAR(64) NOT NULL,
  id_utilisateur INT NULL,
  resultat       ENUM('autorise', 'inconnu', 'refuse', 'erreur') NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE SET NULL
);
