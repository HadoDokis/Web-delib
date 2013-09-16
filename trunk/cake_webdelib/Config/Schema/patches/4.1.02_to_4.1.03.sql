--
-- Début du patch : début de la transaction
--
BEGIN;

ALTER TABLE seances ADD COLUMN numero_depot integer NOT NULL DEFAULT '0';
--
-- Fin du patch : fin de la transaction
--
COMMIT;
