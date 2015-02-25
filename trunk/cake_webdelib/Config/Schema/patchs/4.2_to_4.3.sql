--
-- Début du patch : début de la transaction
--
BEGIN;

UPDATE acos SET alias='Seances:Calendrier' WHERE alias ='Seances:afficherCalendrier';
ALTER TABLE typeseances ADD COLUMN color VARCHAR (7);

ALTER TABLE users RENAME COLUMN login to username;
ALTER TABLE users ADD COLUMN active boolean DEFAULT true;
UPDATE users SET active=true;

ALTER TABLE profils RENAME COLUMN libelle to "name";

ALTER TABLE natures RENAME COLUMN libelle to "name";

ALTER TABLE typeactes RENAME COLUMN libelle to "name";

COMMIT;