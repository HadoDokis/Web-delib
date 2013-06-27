--
-- Début du patch : début de la transaction
--
BEGIN;


ALTER TABLE deliberations_seances ADD COLUMN commentaire character varying(1000) NULL;


ALTER TABLE acteurs_seances ADD COLUMN model varchar(20);
UPDATE acteurs_seances SET model='convocation';
ALTER TABLE acteurs_seances ALTER COLUMN model SET NOT NULL;



//Script à faire

ALTER TABLE listepresences ADD COLUMN suppleant_id integer NULL;
//Script à faire


ALTER TABLE listepresences ALTER COLUMN mandataire DROP NOT NULL;
ALTER TABLE listepresences ALTER COLUMN mandataire DROP DEFAULT;
UPDATE listepresences SET mandataire=NULL WHERE mandataire=0;




UPDATE annexes SET filetype='application/vnd.oasis.opendocument.text' WHERE filetype LIKE '%vnd.oasis.opendocument%';
UPDATE annexes SET filetype='application/pdf' WHERE filetype LIKE '%pdf%';
//Script à faire


--
-- Fin du patch : fin de la transaction
--
COMMIT;
