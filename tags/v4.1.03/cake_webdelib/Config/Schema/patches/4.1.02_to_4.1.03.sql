--
-- Début du patch : début de la transaction
--
BEGIN;

ALTER TABLE seances ADD COLUMN numero_depot integer NOT NULL DEFAULT '0';

ALTER TABLE deliberations ALTER COLUMN num_pref TYPE character varying(255);

ALTER TABLE users   DROP COLUMN IF EXISTS zone_1, 
                    DROP COLUMN IF EXISTS zone_2, 
                    DROP COLUMN IF EXISTS zone_3, 
                    DROP COLUMN IF EXISTS zone_4, 
                    DROP COLUMN IF EXISTS zone_5, 
                    DROP COLUMN IF EXISTS zone_6, 
                    DROP COLUMN IF EXISTS zone_7, 
                    DROP COLUMN IF EXISTS zone_8, 
                    DROP COLUMN IF EXISTS zone_9;

ALTER TABLE collectivites ADD COLUMN logo bytea;

ALTER TABLE infosupdefs ALTER COLUMN val_initiale TYPE character varying(1000);

ALTER TABLE infosups ALTER COLUMN text TYPE character varying(1000);

ALTER TABLE deliberations ALTER COLUMN vote_commentaire TYPE character varying(1000);

ALTER TABLE deliberations ALTER COLUMN num_pref DROP NOT NULL;
ALTER TABLE deliberations ALTER COLUMN reporte SET DEFAULT FALSE;

ALTER TABLE annexes ALTER COLUMN filename TYPE character varying(100);

--
-- Fin du patch : fin de la transaction
--
COMMIT;
