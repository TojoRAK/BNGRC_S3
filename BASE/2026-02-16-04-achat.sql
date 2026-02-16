CREATE TABLE settings (
  `key` VARCHAR(50) PRIMARY KEY,
  `value` VARCHAR(50) NOT NULL
);

INSERT INTO settings VALUES ('taux_frais_achat', '10');

CREATE TABLE achat (
  id_achat INT AUTO_INCREMENT PRIMARY KEY,
  id_ville INT NOT NULL,
  date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
  taux_frais DECIMAL(5,2) NOT NULL,
  total_ht DECIMAL(14,2) NOT NULL,
  total_ttc DECIMAL(14,2) NOT NULL,
  status ENUM('VALIDE','ANNULE') DEFAULT 'VALIDE',

  FOREIGN KEY (id_ville) REFERENCES ville(id_ville)
);

CREATE TABLE achat_ligne (
  id_achat INT,
  id_article INT,
  quantite DECIMAL(14,2) NOT NULL,
  pu DECIMAL(12,2) NOT NULL,
  montant_ht DECIMAL(14,2) NOT NULL,

  PRIMARY KEY (id_achat, id_article),
  FOREIGN KEY (id_achat) REFERENCES achat(id_achat),
  FOREIGN KEY (id_article) REFERENCES article(id_article)
);

CREATE TABLE achat_paiement (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_achat INT NOT NULL,
  id_don_argent INT NOT NULL,
  montant_utilise DECIMAL(14,2) NOT NULL,
  FOREIGN KEY (id_achat) REFERENCES achat(id_achat),
  FOREIGN KEY (id_don_argent) REFERENCES don(id_don)
);
