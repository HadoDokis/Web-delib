--
-- Début du patch : début de la transaction
--
BEGIN;

DROP TABLE  IF EXISTS roles;
CREATE TABLE roles
(
  id serial NOT NULL,
  "name" text NOT NULL,
  CONSTRAINT role_pkey PRIMARY KEY (id)
);

INSERT INTO roles (id, "name") VALUES (1,'Utilisateur');
INSERT INTO roles (id, "name") VALUES (2,'Administrateur');
INSERT INTO roles (id, "name") VALUES (3,'Administrateur fonctionnel');

ALTER TABLE profils ADD COLUMN role_id integer NOT NULL DEFAULT 1;

-- UPDATE profils typesprofil_id=2

UPDATE acos SET alias='Seances:Calendrier' WHERE alias ='Seances:afficherCalendrier';


ALTER TABLE typeseances ADD COLUMN color VARCHAR (7);

ALTER TABLE users RENAME COLUMN login to username;
ALTER INDEX login RENAME TO username;

ALTER TABLE users ADD COLUMN active boolean DEFAULT true;
UPDATE users SET active=true;

ALTER TABLE profils RENAME COLUMN libelle to "name";
ALTER TABLE services RENAME COLUMN libelle to "name";

ALTER TABLE natures RENAME COLUMN libelle to "name";

ALTER TABLE typeactes RENAME COLUMN libelle to "name";

ALTER TABLE deliberations ADD COLUMN tdt_ar_date timestamp without time zone;

DROP TABLE  IF EXISTS users_deliberations;
CREATE TABLE users_deliberations
(
  id serial NOT NULL,
  user_id integer NOT NULL,
  deliberation_id integer NOT NULL,
  CONSTRAINT users_deliberations_pkey PRIMARY KEY (id)
) 

--GESTION DES DROITS
ALTER TABLE aros_acos ADD COLUMN _admin character varying(2) NOT NULL DEFAULT '0'::character varying;
ALTER TABLE aros_acos ADD COLUMN _manage character varying(2) NOT NULL DEFAULT '0'::character varying;

ALTER TABLE deliberations ADD COLUMN vote_prendre_acte BOOLEAN DEFAULT NULL;

COMMIT;
