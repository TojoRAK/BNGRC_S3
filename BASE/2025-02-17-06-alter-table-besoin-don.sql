-- Active: 1742219108388@@127.0.0.1@3306@bngrc_db
-- 1) Ajouter la colonne quantite_initiale
ALTER TABLE don
  ADD COLUMN quantite_initiale DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER id_article;

-- 2) Initialiser quantite_initiale avec la quantité actuelle
UPDATE don
SET quantite_initiale = quantite
WHERE quantite_initiale = 0;

-- 3) Remplacer le CHECK (>0) par (>=0)
ALTER TABLE don
  DROP CHECK chk_don_quantite_positive;

ALTER TABLE don
  ADD CONSTRAINT chk_don_quantite_non_negative CHECK (quantite >= 0);

-- 4) (Optionnel mais recommandé) garder une cohérence minimale
ALTER TABLE don
  ADD CONSTRAINT chk_don_initial_non_negative CHECK (quantite_initiale >= 0),
  ADD CONSTRAINT chk_don_quantite_le_initial CHECK (quantite <= quantite_initiale);


-- 1) Ajouter quantite_initiale
ALTER TABLE besoin_ville
  ADD COLUMN quantite_initiale DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER id_article;

-- 2) Initialiser
UPDATE besoin_ville
SET quantite_initiale = quantite
WHERE quantite_initiale = 0;

-- 3) Remplacer le CHECK (>0) par (>=0)
ALTER TABLE besoin_ville
  DROP CHECK chk_besoin_quantite_positive;

ALTER TABLE besoin_ville
  ADD CONSTRAINT chk_besoin_quantite_non_negative CHECK (quantite >= 0);

-- 4) Cohérence (optionnel mais recommandé)
ALTER TABLE besoin_ville
  ADD CONSTRAINT chk_besoin_initial_non_negative CHECK (quantite_initiale >= 0),
  ADD CONSTRAINT chk_besoin_quantite_le_initial CHECK (quantite <= quantite_initiale);
