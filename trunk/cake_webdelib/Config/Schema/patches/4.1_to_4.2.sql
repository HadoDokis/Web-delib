BEGIN;

-- Mise à jour de la table models
-- ALTER TABLE models ADD COLUMN modeltype_id integer references modeltypes(id);
-- ALTER TABLE models RENAME COLUMN name TO filename;
-- ALTER TABLE models RENAME COLUMN modele TO name;
-- ALTER TABLE models RENAME COLUMN size TO filesize;
-- ALTER TABLE models DROP COLUMN type;
-- ALTER TABLE models DROP COLUMN extension;
-- ALTER TABLE models DROP COLUMN recherche;
-- ALTER TABLE models DROP COLUMN multiodj;

-- Nouvelles notifications utilisateur
ALTER TABLE users ADD COLUMN mail_modif_projet_cree BOOLEAN default false;
ALTER TABLE users ADD COLUMN mail_modif_projet_valide BOOLEAN default false;


COMMIT;