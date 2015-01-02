--
-- Début du patch : début de la transaction
--
BEGIN;

UPDATE acos SET alias='Seances:Calendrier' WHERE alias ='Seances:afficherCalendrier';
ALTER TABLE typeseances ADD COLUMN color VARCHAR (7);
--
COMMIT;