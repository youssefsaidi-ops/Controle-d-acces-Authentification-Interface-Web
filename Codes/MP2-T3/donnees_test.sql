-- Données de test du MP2
-- À lancer après creation.sql

USE mp2_salle215;

-- Les mots de passe seront de vrais mots de passe sécurisés quand l'API sera prête
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, id_role) VALUES
  ('Martin',  'Alice', 'alice.martin@test.fr', 'test_a_remplacer', 1),
  ('Dupont',  'Lucas', 'lucas.dupont@test.fr', 'test_a_remplacer', 2),
  ('Bernard', 'Emma',  'emma.bernard@test.fr', 'test_a_remplacer', 2);

-- Pas de rôle indiqué : doit devenir client automatiquement
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES
  ('Petit', 'Hugo', 'hugo.petit@test.fr', 'test_a_remplacer');

INSERT INTO badges (uid, id_utilisateur, actif) VALUES
  ('A3F2C410', 1, TRUE),
  ('7B19E2D5', 3, TRUE),
  ('C04D8812', 4, FALSE);

INSERT INTO reservations (id_utilisateur, date_debut, date_fin) VALUES
  (2, '2026-10-12 08:00:00', '2026-10-12 10:00:00'),
  (3, '2026-10-12 14:00:00', '2026-10-12 15:30:00');

INSERT INTO qrcodes (code, id_reservation) VALUES
  ('qr_test_0001', 1),
  ('qr_test_0002', 2);

INSERT INTO acces (type, valeur_lue, id_utilisateur, resultat) VALUES
  ('RFID', 'A3F2C410',     1,    'autorise'),
  ('RFID', 'FFFFFFFF',     NULL, 'inconnu'),
  ('RFID', 'C04D8812',     4,    'refuse'),
  ('QR',   'qr_test_0001', 2,    'autorise');
