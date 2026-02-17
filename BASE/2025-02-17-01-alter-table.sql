use bngrc_db;

--ajout de etat partiel dans besoin
ALTER TABLE besoin_ville
MODIFY COLUMN status ENUM(
    "satisfait",
    "non_satisfait",
    "partiel"
) NOT NULL DEFAULT "non_satisfait";

--ajout de etat partiel dans don
ALTER TABLE don
MODIFY COLUMN statut ENUM(
    'NON_DISPATCHE',
    'DISPATCHE',
    'PARTIEL'
) NOT NULL DEFAULT 'NON_DISPATCHE';

ALTER TABLE dispatch
ADD COLUMN 
id_besoin INT NOT NULL;

ALTER TABLE dispatch
ADD CONSTRAINT fk_dispatch_besoin
FOREIGN KEY (id_besoin) REFERENCES besoin_ville(id_besoin);

