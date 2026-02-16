use bngrc_db;

ALTER TABLE besoin_ville
ADD COLUMN status ENUM("satisfait", "non_satisfait") NOT NULL DEFAULT "non_satisfait";