--
-- Début du patch : début de la transaction
--
BEGIN;

-- Mise à jour de la table tdtmessage
ALTER TABLE tdt_messages RENAME message_id TO tdt_id;
ALTER TABLE tdt_messages RENAME type_reponse TO tdt_etat;
ALTER TABLE tdt_messages RENAME type_message TO tdt_type;
ALTER TABLE tdt_messages RENAME "data" TO tdt_data;
ALTER TABLE tdt_messages ADD COLUMN parent_id integer;
-- Ajout de la tdt_AR
ALTER TABLE deliberations ADD COLUMN tdt_AR bytea;

-- Mise à jour du nom et de la description de la tache de conversion des annexes
UPDATE crons SET nom='CONVERSION : conversion des annexes', description='conversion des annexes dans différents formats' WHERE id=7;
--
-- Fin du patch : fin de la transaction
--
COMMIT;