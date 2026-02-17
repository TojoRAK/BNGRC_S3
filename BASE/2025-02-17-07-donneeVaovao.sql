USE bngrc_dons;

START TRANSACTION;

-- Pour éviter les soucis de FK pendant le nettoyage
SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- 1) DELETE / TRUNCATE des tables "mouvements"
-- =========================

Delete from ville;
Delete from achat_paiement;
Delete from achat_ligne;
Delete from achat;

Delete from dispatch;
Delete from besoin_ville;
Delete from don;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- 2) Créer les villes si elles n'existent pas
-- =========================
INSERT INTO ville (id_region, name, nb_sinistres)
SELECT 1, 'Mananjary', 0 WHERE NOT EXISTS (SELECT 1 FROM ville WHERE name='Mananjary');
INSERT INTO ville (id_region, name, nb_sinistres)
SELECT 1, 'Farafangana', 0 WHERE NOT EXISTS (SELECT 1 FROM ville WHERE name='Farafangana');
INSERT INTO ville (id_region, name, nb_sinistres)
SELECT 1, 'Nosy Be', 0 WHERE NOT EXISTS (SELECT 1 FROM ville WHERE name='Nosy Be');
INSERT INTO ville (id_region, name, nb_sinistres)
SELECT 1, 'Morondava', 0 WHERE NOT EXISTS (SELECT 1 FROM ville WHERE name='Morondava');
-- Toamasina est souvent déjà là, sinon :
INSERT INTO ville (id_region, name, nb_sinistres)
SELECT 1, 'Toamasina', 0 WHERE NOT EXISTS (SELECT 1 FROM ville WHERE name='Toamasina');

-- =========================
-- 3) Garantir les articles (si absents)
--    type id FIXE : 1=NATURE, 2=MATERIAUX, 3=ARGENT
-- =========================
-- NATURE
INSERT INTO article (id_type, name, pu)
SELECT 1, 'Riz (kg)', 3000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Riz (kg)');

INSERT INTO article (id_type, name, pu)
SELECT 1, 'Eau (L)', 1000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Eau (L)');

INSERT INTO article (id_type, name, pu)
SELECT 1, 'Huile (L)', 6000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Huile (L)');

INSERT INTO article (id_type, name, pu)
SELECT 1, 'Haricots', 4000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Haricots');

-- MATERIAUX
INSERT INTO article (id_type, name, pu)
SELECT 2, 'Tôle', 25000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Tôle');

INSERT INTO article (id_type, name, pu)
SELECT 2, 'Bâche', 15000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Bâche');

INSERT INTO article (id_type, name, pu)
SELECT 2, 'Clous (kg)', 8000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Clous (kg)');

INSERT INTO article (id_type, name, pu)
SELECT 2, 'Bois', 10000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Bois');

INSERT INTO article (id_type, name, pu)
SELECT 2, 'groupe', 6750000.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='groupe');

-- ARGENT
INSERT INTO article (id_type, name, pu)
SELECT 3, 'Argent', 1.00
WHERE NOT EXISTS (SELECT 1 FROM article WHERE name='Argent');

-- =========================
-- 4) Insert des BESOINS
--    ordre = id_besoin
--    quantite_initiale = quantite
-- =========================
INSERT INTO besoin_ville (id_besoin, id_ville, id_article, quantite_initiale, quantite, date_saisie, status) VALUES
-- Toamasina
(17, (SELECT id_ville FROM ville WHERE name='Toamasina'), (SELECT id_article FROM article WHERE name='Riz (kg)'),     800,      800,      '2026-02-16 00:00:00', 'non_satisfait'),
(4,  (SELECT id_ville FROM ville WHERE name='Toamasina'), (SELECT id_article FROM article WHERE name='Eau (L)'),      1500,     1500,     '2026-02-15 00:00:00', 'non_satisfait'),
(23, (SELECT id_ville FROM ville WHERE name='Toamasina'), (SELECT id_article FROM article WHERE name='Tôle'),         120,      120,      '2026-02-16 00:00:00', 'non_satisfait'),
(1,  (SELECT id_ville FROM ville WHERE name='Toamasina'), (SELECT id_article FROM article WHERE name='Bâche'),        200,      200,      '2026-02-15 00:00:00', 'non_satisfait'),
(12, (SELECT id_ville FROM ville WHERE name='Toamasina'), (SELECT id_article FROM article WHERE name='Argent'),       12000000, 12000000, '2026-02-16 00:00:00', 'non_satisfait'),
(16, (SELECT id_ville FROM ville WHERE name='Toamasina'), (SELECT id_article FROM article WHERE name='groupe'),       3,        3,        '2026-02-15 00:00:00', 'non_satisfait'),

-- Mananjary
(9,  (SELECT id_ville FROM ville WHERE name='Mananjary'), (SELECT id_article FROM article WHERE name='Riz (kg)'),     500,      500,      '2026-02-15 00:00:00', 'non_satisfait'),
(25, (SELECT id_ville FROM ville WHERE name='Mananjary'), (SELECT id_article FROM article WHERE name='Huile (L)'),    120,      120,      '2026-02-16 00:00:00', 'non_satisfait'),
(6,  (SELECT id_ville FROM ville WHERE name='Mananjary'), (SELECT id_article FROM article WHERE name='Tôle'),         80,       80,       '2026-02-15 00:00:00', 'non_satisfait'),
(19, (SELECT id_ville FROM ville WHERE name='Mananjary'), (SELECT id_article FROM article WHERE name='Clous (kg)'),   60,       60,       '2026-02-16 00:00:00', 'non_satisfait'),
(3,  (SELECT id_ville FROM ville WHERE name='Mananjary'), (SELECT id_article FROM article WHERE name='Argent'),       6000000,  6000000,  '2026-02-15 00:00:00', 'non_satisfait'),

-- Farafangana
(21, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Riz (kg)'),    600,      600,      '2026-02-16 00:00:00', 'non_satisfait'),
(14, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Eau (L)'),     1000,     1000,     '2026-02-15 00:00:00', 'non_satisfait'),
(8,  (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Bâche'),       150,      150,      '2026-02-16 00:00:00', 'non_satisfait'),
(26, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Bois'),        100,      100,      '2026-02-15 00:00:00', 'non_satisfait'),
(10, (SELECT id_ville FROM ville WHERE name='Farafangana'), (SELECT id_article FROM article WHERE name='Argent'),      8000000,  8000000,  '2026-02-16 00:00:00', 'non_satisfait'),

-- Nosy Be
(5,  (SELECT id_ville FROM ville WHERE name='Nosy Be'), (SELECT id_article FROM article WHERE name='Riz (kg)'),        300,      300,      '2026-02-15 00:00:00', 'non_satisfait'),
(18, (SELECT id_ville FROM ville WHERE name='Nosy Be'), (SELECT id_article FROM article WHERE name='Haricots'),        200,      200,      '2026-02-16 00:00:00', 'non_satisfait'),
(2,  (SELECT id_ville FROM ville WHERE name='Nosy Be'), (SELECT id_article FROM article WHERE name='Tôle'),            40,       40,       '2026-02-15 00:00:00', 'non_satisfait'),
(24, (SELECT id_ville FROM ville WHERE name='Nosy Be'), (SELECT id_article FROM article WHERE name='Clous (kg)'),      30,       30,       '2026-02-16 00:00:00', 'non_satisfait'),
(7,  (SELECT id_ville FROM ville WHERE name='Nosy Be'), (SELECT id_article FROM article WHERE name='Argent'),          4000000,  4000000,  '2026-02-15 00:00:00', 'non_satisfait'),

-- Morondava
(11, (SELECT id_ville FROM ville WHERE name='Morondava'), (SELECT id_article FROM article WHERE name='Riz (kg)'),       700,      700,      '2026-02-16 00:00:00', 'non_satisfait'),
(20, (SELECT id_ville FROM ville WHERE name='Morondava'), (SELECT id_article FROM article WHERE name='Eau (L)'),        1200,     1200,     '2026-02-15 00:00:00', 'non_satisfait'),
(15, (SELECT id_ville FROM ville WHERE name='Morondava'), (SELECT id_article FROM article WHERE name='Bâche'),          180,      180,      '2026-02-16 00:00:00', 'non_satisfait'),
(22, (SELECT id_ville FROM ville WHERE name='Morondava'), (SELECT id_article FROM article WHERE name='Bois'),           150,      150,      '2026-02-15 00:00:00', 'non_satisfait'),
(13, (SELECT id_ville FROM ville WHERE name='Morondava'), (SELECT id_article FROM article WHERE name='Argent'),         10000000, 10000000, '2026-02-16 00:00:00', 'non_satisfait');

-- Recaler AUTO_INCREMENT pour éviter collision si tu insères ensuite sans id
-- SET @mx_b := (SELECT COALESCE(MAX(id_besoin),0) + 1 FROM besoin_ville);
-- SET @sql_b := CONCAT('ALTER TABLE besoin_ville AUTO_INCREMENT = ', @mx_b);
-- PREPARE stmt_b FROM @sql_b; EXECUTE stmt_b; DEALLOCATE PREPARE stmt_b;

-- =========================
-- 5) Insert des DONS
--    quantite_initiale = quantite
-- =========================
INSERT INTO don (id_article, quantite_initiale, quantite, date_don, source, statut) VALUES
((SELECT id_article FROM article WHERE name='Argent'),    5000000,  5000000,  '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    3000000,  3000000,  '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    4000000,  4000000,  '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    1500000,  1500000,  '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    6000000,  6000000,  '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Riz (kg)'),  400,      400,      '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Eau (L)'),   600,      600,      '2026-02-16 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Tôle'),      50,       50,       '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Bâche'),     70,       70,       '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Haricots'),  100,      100,      '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Riz (kg)'),  2000,     2000,     '2026-02-18 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Tôle'),      300,      300,      '2026-02-18 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Eau (L)'),   5000,     5000,     '2026-02-18 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'),    20000000, 20000000, '2026-02-19 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Bâche'),     500,      500,      '2026-02-19 00:00:00', 'Donateur', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Haricots'),  88,       88,       '2026-02-17 00:00:00', 'Donateur', 'NON_DISPATCHE');

COMMIT;
