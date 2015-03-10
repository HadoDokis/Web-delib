--
-- Début du patch : début de la transaction
--
BEGIN;

UPDATE annexes SET filetype='application/msword' WHERE filename LIKE '%.doc';
UPDATE annexes SET filetype='application/vnd.openxmlformats-officedocument.wordprocessingml.document' WHERE filename LIKE '%.docx';
UPDATE annexes SET filetype='application/vnd.ms-excel', joindre_fusion=false WHERE filename LIKE '%.xls';
UPDATE annexes SET filetype='application/pdf' WHERE filename LIKE '%.pdf';
UPDATE annexes SET filetype='application/vnd.oasis.opendocument.text' WHERE filename LIKE '%.odt';
UPDATE annexes SET filetype='application/vnd.oasis.opendocument.spreadsheet', joindre_fusion=false WHERE filename LIKE '%.ods';

ALTER TABLE IF EXISTS crons DROP CONSTRAINT IF EXISTS crons_pkey,
ADD CONSTRAINT crons_pkey PRIMARY KEY(id),
ALTER COLUMN "lock" TYPE boolean,
ALTER COLUMN "lock" SET NOT NULL,
ALTER COLUMN "lock" DROP DEFAULT;

--
-- Fin du patch : fin de la transaction
--
COMMIT;