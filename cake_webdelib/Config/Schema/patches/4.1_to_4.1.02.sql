--
-- Début du patch : début de la transaction
--
BEGIN;

ALTER TABLE deliberations_seances ADD COLUMN commentaire character varying(1000) NULL;

ALTER TABLE deliberations ADD COLUMN id_parapheur varchar(32);

--
-- Fin du patch : fin de la transaction
--
COMMIT;
