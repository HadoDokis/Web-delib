--
-- Début du patch : début de la transaction
--
BEGIN;

ALTER TABLE seances ADD COLUMN numero_depot integer NOT NULL DEFAULT '0';

ALTER TABLE deliberations ALTER COLUMN num_pref TYPE character varying(255) NOT NULL;

ALTER TABLE users   DROP COLUMN zone_1, 
                    DROP COLUMN zone_2, 
                    DROP COLUMN zone_3, 
                    DROP COLUMN zone_4, 
                    DROP COLUMN zone_5, 
                    DROP COLUMN zone_6, 
                    DROP COLUMN zone_7, 
                    DROP COLUMN zone_8, 
                    DROP COLUMN zone_9;

--
-- Fin du patch : fin de la transaction
--
COMMIT;
