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

DROP TABLE  IF EXISTS users_deliberations;
CREATE TABLE users_deliberations
(
  id serial NOT NULL,
  user_id integer NOT NULL,
  deliberation_id integer NOT NULL,
  CONSTRAINT users_deliberations_pkey PRIMARY KEY (id)
) 

ALTER TABLE deliberations ADD COLUMN vote_prendre_acte BOOLEAN DEFAULT NULL;

COMMIT;