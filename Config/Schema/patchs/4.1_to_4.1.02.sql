--
-- Début du patch : début de la transaction
--
BEGIN;

ALTER TABLE deliberations ADD COLUMN id_parapheur varchar(50);
ALTER TABLE deliberations ALTER COLUMN titre DROP NOT NULL;
ALTER TABLE deliberations ALTER COLUMN objet_delib DROP NOT NULL;

ALTER TABLE deliberations_seances ADD COLUMN commentaire character varying(1000) NULL;

ALTER TABLE infosupdefs DROP COLUMN taille;


ALTER TABLE acteurs_seances ADD COLUMN model varchar(20);
UPDATE acteurs_seances SET model='convocation';
ALTER TABLE acteurs_seances ALTER COLUMN model SET NOT NULL;



--Script à faire

ALTER TABLE listepresences ADD COLUMN suppleant_id integer NULL;
--Script à faire


ALTER TABLE listepresences ALTER COLUMN mandataire DROP NOT NULL;
ALTER TABLE listepresences ALTER COLUMN mandataire DROP DEFAULT;
UPDATE listepresences SET mandataire=NULL WHERE mandataire=0;




UPDATE annexes SET filetype='application/vnd.oasis.opendocument.text' WHERE filetype LIKE '%vnd.oasis.opendocument%';
UPDATE annexes SET filetype='application/pdf' WHERE filetype LIKE '%pdf%';

--Pour que les anciennes annexes soient générées en ODT
UPDATE annexes SET data_pdf=data WHERE filetype='application/pdf' AND filename NOT LIKE '%odt%';
UPDATE annexes SET filename_pdf=filename WHERE filetype='application/pdf' AND filename NOT LIKE '%odt%';
UPDATE annexes SET filename=CONCAT(substring(filename_pdf, 0, 72),'.odt') WHERE filetype='application/pdf' AND filename NOT LIKE '%odt%';
--Script à faire


--
-- Fin du patch : fin de la transaction
--
COMMIT;
