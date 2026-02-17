-- Active: 1742219108388@@127.0.0.1@3306@bngrc_db
-- =========================
-- 1) DATABASE
-- =========================
CREATE DATABASE IF NOT EXISTS bngrc_db;

USE bngrc_db;

DROP TABLE IF EXISTS dispatch;
DROP TABLE IF EXISTS don;
DROP TABLE IF EXISTS besoin_ville;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS type_besoin;
DROP TABLE IF EXISTS ville;
DROP TABLE IF EXISTS region;
SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- 2) TABLES
-- =========================

CREATE TABLE region (
  id_region INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE ville (
  id_ville INT AUTO_INCREMENT PRIMARY KEY,
  id_region INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  nb_sinistres INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_ville_region
    FOREIGN KEY (id_region) REFERENCES region(id_region)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_ville_region_name (id_region, name)
);

CREATE TABLE type_besoin (
  id_type INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE article (
  id_article INT AUTO_INCREMENT PRIMARY KEY,
  id_type INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  pu DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_article_type
    FOREIGN KEY (id_type) REFERENCES type_besoin(id_type)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_article (id_type, name),
  CONSTRAINT chk_article_pu_positive CHECK (pu > 0)
);

CREATE TABLE besoin_ville (
  id_besoin INT AUTO_INCREMENT PRIMARY KEY,
  id_ville INT NOT NULL,
  id_article INT NOT NULL,
  quantite DECIMAL(14,2) NOT NULL,
  date_saisie DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_besoin_ville
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_besoin_article
    FOREIGN KEY (id_article) REFERENCES article(id_article)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_don_quantite_non_negative CHECK (quantite >= 0)
);

CREATE TABLE don (
  id_don INT AUTO_INCREMENT PRIMARY KEY,
  id_article INT NOT NULL,
  quantite DECIMAL(14,2) NOT NULL,
  date_don DATETIME NOT NULL,
  source VARCHAR(120) NULL,
  statut ENUM('NON_DISPATCHE','DISPATCHE') NOT NULL DEFAULT 'NON_DISPATCHE',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_don_article
    FOREIGN KEY (id_article) REFERENCES article(id_article)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_don_quantite_non_negative CHECK (quantite >= 0)
);

CREATE TABLE dispatch (
  id_dispatch INT AUTO_INCREMENT PRIMARY KEY,
  id_don INT NOT NULL,
  id_ville INT NOT NULL,
  quantite_attribuee DECIMAL(14,2) NOT NULL,
  date_dispatch DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_dispatch_don
    FOREIGN KEY (id_don) REFERENCES don(id_don)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_dispatch_ville
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_dispatch_quantite_positive CHECK (quantite_attribuee > 0)
);

