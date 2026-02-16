-- Active: 1742219108388@@127.0.0.1@3306@takalo_takalo
-- =========================
-- Takalo-takalo (MySQL)
-- Création + données de test
-- =========================

-- (Optionnel) créer la base
CREATE DATABASE IF NOT EXISTS takalo_takalo
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE takalo_takalo;

-- (Optionnel) nettoyer si tu relances le script
DROP TABLE IF EXISTS echange;
DROP TABLE IF EXISTS proposition;
DROP TABLE IF EXISTS histo_propietaire;
DROP TABLE IF EXISTS objet_image;
DROP TABLE IF EXISTS objet;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- =========================
-- 1) TABLES
-- =========================

CREATE TABLE users (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(120) NOT NULL,
  role          ENUM('ADMIN','USER') NOT NULL DEFAULT 'USER',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  password_hash VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
ALTER TABLE users ADD COLUMN email VARCHAR(120) UNIQUE;

CREATE TABLE categories (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(80) NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE objet (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title           VARCHAR(160) NOT NULL,
  description     TEXT,
  estimated_value DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  owner_user_id   BIGINT UNSIGNED NOT NULL,
  category_id     BIGINT UNSIGNED NOT NULL,
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),

  KEY idx_objet_owner (owner_user_id),
  KEY idx_objet_category (category_id),

  CONSTRAINT fk_objet_owner
    FOREIGN KEY (owner_user_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_objet_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE objet_image (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  objet_id   BIGINT UNSIGNED NOT NULL,
  path       VARCHAR(255) NOT NULL,
  is_main    TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  -- Astuce MySQL: on génère une colonne NULL sauf si is_main=1
  -- et on met un UNIQUE(objet_id, main_rank) => plusieurs NULL autorisés,
  -- donc plusieurs images non-main possibles, mais une seule main.
  main_rank  TINYINT GENERATED ALWAYS AS (CASE WHEN is_main = 1 THEN 1 ELSE NULL END) STORED,

  PRIMARY KEY (id),
  KEY idx_objet_image_objet (objet_id),
  UNIQUE KEY uq_one_main_per_objet (objet_id, main_rank),

  CONSTRAINT fk_objet_image_objet
    FOREIGN KEY (objet_id) REFERENCES objet(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE proposition (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  requester_id  BIGINT UNSIGNED NOT NULL,
  owner_id      BIGINT UNSIGNED NOT NULL,
  wanted_id     BIGINT UNSIGNED NOT NULL,
  offered_id    BIGINT UNSIGNED NOT NULL,
  message       TEXT,
  status        ENUM('PENDING','ACCEPTED','REJECTED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  responded_at  TIMESTAMP NULL DEFAULT NULL,

  PRIMARY KEY (id),

  KEY idx_prop_status (status),
  KEY idx_prop_owner (owner_id),
  KEY idx_prop_requester (requester_id),
  KEY idx_prop_wanted (wanted_id),
  KEY idx_prop_offered (offered_id),

  CONSTRAINT fk_prop_requester
    FOREIGN KEY (requester_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_prop_owner
    FOREIGN KEY (owner_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_prop_wanted
    FOREIGN KEY (wanted_id) REFERENCES objet(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_prop_offered
    FOREIGN KEY (offered_id) REFERENCES objet(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_prop_objects_diff CHECK (wanted_id <> offered_id)
) ENGINE=InnoDB;

CREATE TABLE echange (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  proposition_id  BIGINT UNSIGNED NOT NULL,
  traded_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_trade_one_prop (proposition_id),

  CONSTRAINT fk_echange_prop
    FOREIGN KEY (proposition_id) REFERENCES proposition(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE histo_propietaire (
  id        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  objet_id  BIGINT UNSIGNED NOT NULL,
  user_id   BIGINT UNSIGNED NOT NULL,
  start_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_histo_objet (objet_id),
  KEY idx_histo_user (user_id),

  CONSTRAINT fk_histo_objet
    FOREIGN KEY (objet_id) REFERENCES objet(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_histo_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- 2) DONNÉES DE TEST
-- =========================

-- Users (password_hash = placeholders)
INSERT INTO users (email, name, role, password_hash) VALUES
('admin@takalo.tld','Admin', 'ADMIN', '$2y$12$wXj6MQUbuDFDU4B2/H6mHe7QHKm4mqQmYnSFaDwEUgRI8UhgY0Hr6'),
('testA@mail.com','Aina',  'USER',  '$2y$10$ainahash_placeholder____________________________'),
('testK@mail.com','Koto',  'USER',  '$2y$10$kotohash_placeholder____________________________'),
('testM@mail.com','Mina',  'USER',  '$2y$10$minahash_placeholder____________________________');

-- Categories
INSERT INTO categories (name) VALUES
('Vêtements'),
('Livres'),
('DVD'),
('Électronique');

-- Objets (owner_user_id: Aina=2, Koto=3, Mina=4)
INSERT INTO objet (title, description, estimated_value, owner_user_id, category_id) VALUES
('Veste en jean', 'Taille M, bon état', 35000, 2, 1),
('Roman: Le Petit Prince', 'Édition poche', 15000, 2, 2),
('DVD Inception', 'Boîte d’origine', 20000, 3, 3),
('Casque audio', 'Bluetooth, autonomie 10h', 60000, 3, 4),
('T-shirt noir', 'Taille L, neuf', 20000, 4, 1),
('Livre Python débutant', 'Livre + exercices', 30000, 4, 2);

-- Images (une principale par objet)
-- INSERT INTO objet_image (objet_id, path, is_main) VALUES_
