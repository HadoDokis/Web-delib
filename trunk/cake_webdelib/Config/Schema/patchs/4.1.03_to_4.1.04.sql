--
-- Début du patch : début de la transaction
--
BEGIN;

ALTER TABLE deliberations ADD COLUMN tdt_data_pdf bytea;

ALTER TABLE deliberations RENAME COLUMN bordereau TO tdt_data_bordereau_pdf;

--
-- Fin du patch : fin de la transaction
--
COMMIT;
