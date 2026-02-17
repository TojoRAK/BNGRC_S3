USE bngrc_dons;

START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- 1) Nettoyage (enfants -> parents)
-- =========================
DELETE FROM dispatch;
DELETE FROM besoin_ville;
DELETE FROM don;

DELETE FROM achat_paiement;
DELETE FROM achat_ligne;
DELETE FROM achat;

DELETE FROM ville;

-- (Optionnel) reset auto_increment si tu veux repartir proprement
ALTER TABLE dispatch AUTO_INCREMENT = 1;
ALTER TABLE besoin_ville AUTO_INCREMENT = 1;
ALTER TABLE don AUTO_INCREMENT = 1;
ALTER TABLE achat_paiement AUTO_INCREMENT = 1;
ALTER TABLE achat_ligne AUTO_INCREMENT = 1;
ALTER TABLE achat AUTO_INCREMENT = 1;
ALTER TABLE ville AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- 2) Villes (depuis Excel)
--    Nécessite idéalement: UNIQUE(ville.name)
-- =========================
INSERT INTO ville (id_region, name, nb_sinistres) VALUES (1, 'Farafangana', 0)
ON DUPLICATE KEY UPDATE id_region=VALUES(id_region), nb_sinistres=VALUES(nb_sinistres);

INSERT INTO ville (id_region, name, nb_sinistres) VALUES (1, 'Mananjary', 0)
ON DUPLICATE KEY UPDATE id_region=VALUES(id_region), nb_sinistres=VALUES(nb_sinistres);

INSERT INTO ville (id_region, name, nb_sinistres) VALUES (1, 'Morondava', 0)
ON DUPLICATE KEY UPDATE id_region=VALUES(id_region), nb_sinistres=VALUES(nb_sinistres);

INSERT INTO ville (id_region, name, nb_sinistres) VALUES (1, 'Nosy Be', 0)
ON DUPLICATE KEY UPDATE id_region=VALUES(id_region), nb_sinistres=VALUES(nb_sinistres);

INSERT INTO ville (id_region, name, nb_sinistres) VALUES (1, 'Toamasina', 0)
ON DUPLICATE KEY UPDATE id_region=VALUES(id_region), nb_sinistres=VALUES(nb_sinistres);

-- =========================
-- 3) Articles (depuis Excel)
--    mapping type: 1=nature, 2=materiel, 3=argent
--    Nécessite idéalement: UNIQUE(article.name)
-- =========================
INSERT INTO article (id_type, name, pu) VALUES (1, 'Eau (L)', 1000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (1, 'Haricots', 4000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (1, 'Huile (L)', 6000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (1, 'Riz (kg)', 3000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (2, 'Bâche', 15000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (2, 'Bois', 10000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (2, 'Clous (kg)', 8000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (2, 'Tôle', 25000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (2, 'groupe', 6750000.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

INSERT INTO article (id_type, name, pu) VALUES (3, 'Argent', 1.00)
ON DUPLICATE KEY UPDATE id_type=VALUES(id_type), pu=VALUES(pu);

-- =========================
-- 4) Besoins (26 lignes depuis Excel)
--    ordre = id_besoin
--    quantite_initiale = quantite
-- =========================
INSERT INTO besoin_ville
(id_besoin, id_ville, id_article, quantite_initiale, quantite, date_saisie, status)
VALUES
(1,  (SELECT id_ville FROM ville WHERE name='Toamasina'),   (SELECT id_article FROM article WHERE name='Bâche'),       200.00, 200.00, '2026-02-15 00:00:00', 'non_satisfait'),
(2,  (SELECT id_ville FROM ville WHERE name='Nosy Be'),     (SELECT id_article FROM article WHERE name='Tôle'),        40.00,  40.00,  '2026-02-15 00:00:00', 'non_satisfait'),
(3,  (SELECT id_ville FROM ville WHERE name='Mananjary'),   (SELECT id_article FROM article WHERE name='Argent'),      6000000.00, 6000000.00, '2026-02-15 00:00:00', 'non_satisfait'),
(4,  (SELECT id_ville FROM ville WHERE name='Toamasina'),   (SELECT id_article FROM article WHERE name='Eau (L)'),     1500.00, 1500.00, '2026-02-15 00:00:00', 'non_satisfait'),
(5,  (SELECT id_ville FROM ville WHERE name='Nosy Be'),     (SELECT id_article FROM article WHERE name='Riz (kg)'),    300.00, 300.00, '2026-02-15 00:00:00', 'non_satisfait'),
(6,  (SELECT id_ville FROM ville WHERE name='Mananjary'),   (SELECT id_article FROM article WHERE name='Tôle'),        80.00,  80.00,  '2026-02-15 00:00:00', 'non_satisfait'),
(7,  (SELECT id_ville FROM ville WHERE name='Nosy Be'),     (SELECT id_article FROM article WHERE name='Argent'),      4000000.00, 4000000.00, '2026-02-15 00:00:00', 'non_satisfait'),
(8,  (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Bâche'),       150.00, 150.00, '2026-02-16 00:00:00', 'non_satisfait'),
(9,  (SELECT id_ville FROM ville WHERE name='Mananjary'),   (SELECT id_article FROM article WHERE name='Riz (kg)'),    500.00, 500.00, '2026-02-15 00:00:00', 'non_satisfait'),
(10, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Argent'),      8000000.00, 8000000.00, '2026-02-16 00:00:00', 'non_satisfait'),
(11, (SELECT id_ville FROM ville WHERE name='Morondava'),   (SELECT id_article FROM article WHERE name='Riz (kg)'),    700.00, 700.00, '2026-02-16 00:00:00', 'non_satisfait'),
(12, (SELECT id_ville FROM ville WHERE name='Toamasina'),   (SELECT id_article FROM article WHERE name='Argent'),      12000000.00, 12000000.00, '2026-02-16 00:00:00', 'non_satisfait'),
(13, (SELECT id_ville FROM ville WHERE name='Morondava'),   (SELECT id_article FROM article WHERE name='Argent'),      10000000.00, 10000000.00, '2026-02-16 00:00:00', 'non_satisfait'),
(14, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Eau (L)'),     1000.00, 1000.00, '2026-02-15 00:00:00', 'non_satisfait'),
(15, (SELECT id_ville FROM ville WHERE name='Morondava'),   (SELECT id_article FROM article WHERE name='Bâche'),       180.00, 180.00, '2026-02-16 00:00:00', 'non_satisfait'),
(16, (SELECT id_ville FROM ville WHERE name='Toamasina'),   (SELECT id_article FROM article WHERE name='groupe'),      3.00,   3.00,   '2026-02-15 00:00:00', 'non_satisfait'),
(17, (SELECT id_ville FROM ville WHERE name='Toamasina'),   (SELECT id_article FROM article WHERE name='Riz (kg)'),    800.00, 800.00, '2026-02-16 00:00:00', 'non_satisfait'),
(18, (SELECT id_ville FROM ville WHERE name='Nosy Be'),     (SELECT id_article FROM article WHERE name='Haricots'),    200.00, 200.00, '2026-02-16 00:00:00', 'non_satisfait'),
(19, (SELECT id_ville FROM ville WHERE name='Mananjary'),   (SELECT id_article FROM article WHERE name='Clous (kg)'),  60.00,  60.00,  '2026-02-16 00:00:00', 'non_satisfait'),
(20, (SELECT id_ville FROM ville WHERE name='Morondava'),   (SELECT id_article FROM article WHERE name='Eau (L)'),     1200.00, 1200.00, '2026-02-15 00:00:00', 'non_satisfait'),
(21, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Riz (kg)'),    600.00, 600.00, '2026-02-16 00:00:00', 'non_satisfait'),
(22, (SELECT id_ville FROM ville WHERE name='Morondava'),   (SELECT id_article FROM article WHERE name='Bois'),        150.00, 150.00, '2026-02-15 00:00:00', 'non_satisfait'),
(23, (SELECT id_ville FROM ville WHERE name='Toamasina'),   (SELECT id_article FROM article WHERE name='Tôle'),        120.00, 120.00, '2026-02-16 00:00:00', 'non_satisfait'),
(24, (SELECT id_ville FROM ville WHERE name='Nosy Be'),     (SELECT id_article FROM article WHERE name='Clous (kg)'),  30.00,  30.00,  '2026-02-16 00:00:00', 'non_satisfait'),
(25, (SELECT id_ville FROM ville WHERE name='Mananjary'),   (SELECT id_article FROM article WHERE name='Huile (L)'),   120.00, 120.00, '2026-02-16 00:00:00', 'non_satisfait'),
(26, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Bois'),        100.00, 100.00, '2026-02-15 00:00:00', 'non_satisfait');

-- Recaler l'AUTO_INCREMENT de besoin_ville (important car id_besoin est forcé)
SET @mx_b := (SELECT COALESCE(MAX(id_besoin),0) + 1 FROM besoin_ville);
SET @sql_b := CONCAT('ALTER TABLE besoin_ville AUTO_INCREMENT = ', @mx_b);
PREPARE stmt_b FROM @sql_b; EXECUTE stmt_b; DEALLOCATE PREPARE stmt_b;

-- =========================
-- 5) Dons (16 lignes depuis Excel)
-- =========================
INSERT INTO don (id_article, quantite_initiale, quantite, date_don, source, statut) VALUES
((SELECT id_article FROM article WHERE name='Argent'),    5000000.00,  5000000.00,  '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    3000000.00,  3000000.00,  '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    4000000.00,  4000000.00,  '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    1500000.00,  1500000.00,  '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    6000000.00,  6000000.00,  '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Riz (kg)'),  400.00,      400.00,      '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Eau (L)'),   600.00,      600.00,      '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Tôle'),      50.00,       50.00,       '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Bâche'),     70.00,       70.00,       '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Haricots'),  100.00,      100.00,      '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Riz (kg)'),  2000.00,     2000.00,     '2026-02-18 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Tôle'),      300.00,      300.00,      '2026-02-18 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Eau (L)'),   5000.00,     5000.00,     '2026-02-18 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    20000000.00, 20000000.00, '2026-02-19 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Bâche'),     500.00,      500.00,      '2026-02-19 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Haricots'),  88.00,       88.00,       '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE');

COMMIT;
