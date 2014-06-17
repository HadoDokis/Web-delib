--
-- Début du patch : début de la transaction
--
BEGIN;

-- Mise à jour de la table tdtmessage
ALTER TABLE tdt_messages RENAME message_id TO tdt_id;
ALTER TABLE tdt_messages RENAME type_reponse TO tdt_etat;
ALTER TABLE tdt_messages RENAME type_message TO tdt_type;
ALTER TABLE tdt_messages RENAME "data" TO tdt_data;


ALTER TABLE deliberations ADD COLUMN tdt_AR bytea;
--
-- Fin du patch : fin de la transaction
--
COMMIT;