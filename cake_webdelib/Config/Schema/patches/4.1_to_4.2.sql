BEGIN;

-- Mise à jour de la table models
ALTER TABLE models ADD COLUMN modeltype_id INTEGER REFERENCES modeltypes(id) DEFAULT 1;
ALTER TABLE models RENAME COLUMN name TO filename;
ALTER TABLE models RENAME COLUMN modele TO name;
ALTER TABLE models RENAME COLUMN size TO filesize;
ALTER TABLE models DROP COLUMN type;
ALTER TABLE models DROP COLUMN extension;
ALTER TABLE models DROP COLUMN recherche;
ALTER TABLE models DROP COLUMN multiodj;
ALTER TABLE models DROP COLUMN joindre_annexe;

-- Drop important quand le script sql du plugin ModelOdtValidator passe avant (ex: PatchShell)
DROP TABLE IF EXISTS modeltemplates;
ALTER TABLE models RENAME TO modeltemplates;

-- Nouvelles notifications utilisateur
ALTER TABLE users ADD COLUMN mail_modif_projet_cree BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN mail_modif_projet_valide BOOLEAN DEFAULT FALSE;

-- Mise à jour : joindre les annexes par défaut
UPDATE annexes SET joindre_fusion=TRUE;

-- Gabarits textes
ALTER TABLE typeactes ADD COLUMN gabarit_projet BYTEA DEFAULT NULL;
ALTER TABLE typeactes ADD COLUMN gabarit_synthese BYTEA DEFAULT NULL;
ALTER TABLE typeactes ADD COLUMN gabarit_acte BYTEA DEFAULT NULL;

-- Président d'affaire
ALTER TABLE deliberations ADD COLUMN president_id INTEGER DEFAULT NULL;

COMMIT;