USE bngrc_dons;

-- =========================
-- REGIONS
-- =========================
INSERT INTO region (name) VALUES
('Analamanga'),
('Atsinanana'),
('Haute Matsiatra');

-- =========================
-- VILLES
-- =========================
INSERT INTO ville (id_region, name, nb_sinistres) VALUES
(1, 'Antananarivo', 1200),
(1, 'Ambohidratrimo', 350),
(2, 'Toamasina', 800),
(3, 'Fianarantsoa', 500);

-- =========================
-- TYPES
-- =========================
INSERT INTO type_besoin (name) VALUES
('NATURE'),
('MATERIAUX'),
('ARGENT');

-- =========================
-- ARTICLES (PU FIXE)
-- Rappel: ARGENT -> pu=1 et quantite = montant saisi
-- =========================
INSERT INTO article (id_type, name, pu) VALUES
((SELECT id_type FROM type_besoin WHERE name='NATURE'),    'Riz (kg)', 3500.00),
((SELECT id_type FROM type_besoin WHERE name='NATURE'),    'Huile (L)', 9000.00),
((SELECT id_type FROM type_besoin WHERE name='MATERIAUX'), 'Tôle (unité)', 25000.00),
((SELECT id_type FROM type_besoin WHERE name='MATERIAUX'), 'Clou (kg)', 12000.00),
((SELECT id_type FROM type_besoin WHERE name='ARGENT'),    'Argent', 1.00);

-- =========================
-- BESOINS (par ville)
-- =========================
-- Antananarivo : riz, huile, argent
INSERT INTO besoin_ville (id_ville, id_article, quantite, date_saisie) VALUES
((SELECT id_ville FROM ville WHERE name='Antananarivo'),
 (SELECT id_article FROM article WHERE name='Riz (kg)'),
 800, '2026-02-16 13:10:00'),

((SELECT id_ville FROM ville WHERE name='Antananarivo'),
 (SELECT id_article FROM article WHERE name='Huile (L)'),
 120, '2026-02-16 13:11:00'),

((SELECT id_ville FROM ville WHERE name='Antananarivo'),
 (SELECT id_article FROM article WHERE name='Argent'),
 1500000, '2026-02-16 13:12:00');

-- Toamasina : tôle, clou, argent
INSERT INTO besoin_ville (id_ville, id_article, quantite, date_saisie) VALUES
((SELECT id_ville FROM ville WHERE name='Toamasina'),
 (SELECT id_article FROM article WHERE name='Tôle (unité)'),
 60, '2026-02-16 13:13:00'),

((SELECT id_ville FROM ville WHERE name='Toamasina'),
 (SELECT id_article FROM article WHERE name='Clou (kg)'),
 25, '2026-02-16 13:14:00'),

((SELECT id_ville FROM ville WHERE name='Toamasina'),
 (SELECT id_article FROM article WHERE name='Argent'),
 2200000, '2026-02-16 13:15:00');

-- Fianarantsoa : riz
INSERT INTO besoin_ville (id_ville, id_article, quantite, date_saisie) VALUES
((SELECT id_ville FROM ville WHERE name='Fianarantsoa'),
 (SELECT id_article FROM article WHERE name='Riz (kg)'),
 300, '2026-02-16 13:16:00');

-- =========================
-- DONS (non dispatchés au départ)
-- =========================
INSERT INTO don (id_article, quantite, date_don, source, statut) VALUES
((SELECT id_article FROM article WHERE name='Riz (kg)'), 500, '2026-02-16 14:00:00', 'Association Aina', 'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Riz (kg)'), 400, '2026-02-16 14:30:00', 'Donateur privé',  'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Tôle (unité)'), 40, '2026-02-16 15:00:00', 'ONG Build',      'NON_DISPATCHE'),
((SELECT id_article FROM article WHERE name='Argent'), 1000000, '2026-02-16 15:10:00', 'Entreprise X',    'NON_DISPATCHE');
