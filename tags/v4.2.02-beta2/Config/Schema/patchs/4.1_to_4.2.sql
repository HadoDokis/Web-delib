BEGIN;

-- Mise à jour de la table models
ALTER TABLE models RENAME COLUMN name TO filename;
ALTER TABLE models RENAME COLUMN modele TO name;
ALTER TABLE models RENAME COLUMN size TO filesize;
ALTER TABLE models DROP COLUMN type;
ALTER TABLE models DROP COLUMN extension;
ALTER TABLE models DROP COLUMN recherche;
ALTER TABLE models DROP COLUMN multiodj;
ALTER TABLE models DROP COLUMN joindre_annexe;

-- Drop important quand le script sql du plugin ModelOdtValidator passe avant (ex: PatchShell)
DROP TABLE IF EXISTS modeltemplates;
ALTER TABLE models RENAME TO modeltemplates;

ALTER SEQUENCE models_id_seq RENAME TO modeltemplates_id_seq;
ALTER SEQUENCE modeltemplates_id_seq OWNED BY modeltemplates.id;

-- Nouvelles notifications utilisateur
ALTER TABLE users ADD COLUMN mail_modif_projet_cree BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN mail_modif_projet_valide BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN mail_retard_validation BOOLEAN DEFAULT FALSE;

-- Mise à jour : joindre les annexes par défaut
UPDATE annexes
SET joindre_fusion = true WHERE filetype='application/pdf' OR filetype='application/vnd.oasis.opendocument.text';

-- Nouvelle gestion des annexes
ALTER TABLE annexes ADD COLUMN edition_data bytea;
ALTER TABLE annexes ADD COLUMN edition_data_typemime VARCHAR DEFAULT NULL;

--Récupération des annexes PDF source dans data
UPDATE annexes SET data=data_pdf, filename=filename_pdf WHERE filetype='application/pdf' AND data_pdf IS NOT NULL AND filename_pdf IS NOT NULL;
UPDATE annexes SET data_pdf=NULL;
ALTER TABLE annexes DROP COLUMN filename_pdf;

-- Gabarits textes
ALTER TABLE typeactes ADD COLUMN gabarit_projet BYTEA DEFAULT NULL;
ALTER TABLE typeactes ADD COLUMN gabarit_synthese BYTEA DEFAULT NULL;
ALTER TABLE typeactes ADD COLUMN gabarit_acte BYTEA DEFAULT NULL;

-- Président d'affaire
ALTER TABLE deliberations ADD COLUMN president_id INTEGER DEFAULT NULL;

-- Télé-transmissible
ALTER TABLE typeactes ADD COLUMN teletransmettre BOOL DEFAULT TRUE;

-- Crons
ALTER TABLE crons DROP COLUMN controller;
ALTER TABLE crons ADD COLUMN model VARCHAR DEFAULT 'CronJob';

-- Tâche planifiée de mise à jour des délégations
UPDATE crons
SET action = 'delegationJob', plugin = NULL
WHERE id = 1;

ALTER TABLE crons ADD PRIMARY KEY (id);
-- Tâche planifiée de déclenchement des alertes de retard (workflow)
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
VALUES ('2', 'Circuits de traitement : Déclenchement des alertes de retard',
        'Relance les utilisateurs devant valider un projet en retard', NULL, 'CronJob', 'retardCakeflowJob', 'f', NULL,
        now(), 'P1D', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', NULL, TRUE, now(), '1', now(), '1');

-- Tâche planifiée de mise à jour du status des actes envoyés au i-parapheur
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
VALUES ('3', 'Signature : Mise à jour de l''etat des dossiers envoyés au Parapheur',
        'Met à jour l''état des projets envoyés au Parapheur pour signature et rapatrie les informations de ceux en fin de circuit.',
        NULL, 'CronJob', 'signatureJob', 'f', NULL, now(), 'P1D', now(), now(),
        'Cette tâche n''a encore jamais été exécutée.', NULL, TRUE, now(), '1', now(), '1');

-- Tâche planifiée de mise à jour du status des actes envoyés au i-parapheur
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
VALUES
  ('4', 'TDT : Mise à jours des mails sécurisés', 'Envoi/Réception des mails sécurisés', NULL, 'CronJob', 'mailSecJob',
   'f', NULL, now(), 'PT5M', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', NULL, TRUE, now(), '1',
   now(), '1');

-- Tâche planifiée de mise à jour es accusés de reception
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
VALUES ('5', 'TDT : Mise à jour des accusés de réception',
        'Vérifie la réception par la prefecture des dossiers envoyés via le TDT et dans le cas échéant, enregistre la date de l''accusé de réception et le bordereau',
        NULL, 'CronJob', 'majArTdt', 'f', NULL, now(), 'PT5M', now(), now(),
        'Cette tâche n''a encore jamais été exécutée.', NULL, TRUE, now(), '1', now(), '1');

-- Tâche planifiée de mise à jour es accusés de reception
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
VALUES ('6', 'TDT : Mise à jour des échanges de courriers',
        'Met à jour les échanges de courriers entre la préfecture et le TDT', NULL, 'CronJob', 'majCourriersTdt', 'f',
        NULL, now(), 'P1D', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', NULL, TRUE, now(), '1', now(),
        '1');

-- Tâche planifiée pour la convertion des annnexes
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
   VALUES ('7', 'CONVERTION : convertion des annexes', 'convertion des annexes dans différents formats', null, 'CronJob',	'convertionAnnexesJob',	'f', null, now(), 'P1D', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', null,	true, now(), '1', now(), '1');

ALTER TABLE crons ADD COLUMN lock BOOL DEFAULT FALSE NOT NULL;

-- Mise à jour de la table models
ALTER TABLE deliberations RENAME COLUMN etat_asalae TO sae_etat;
ALTER TABLE deliberations RENAME COLUMN etat_parapheur TO parapheur_etat;
ALTER TABLE deliberations RENAME COLUMN id_parapheur TO parapheur_id;
ALTER TABLE deliberations RENAME COLUMN commentaire_refus_parapheur TO parapheur_commentaire;
ALTER TABLE deliberations RENAME COLUMN "dateAR" TO "tdt_dateAR";
ALTER TABLE deliberations ADD COLUMN parapheur_cible VARCHAR DEFAULT NULL;
ALTER TABLE deliberations ALTER COLUMN tdt_id TYPE VARCHAR;
ALTER TABLE deliberations ADD COLUMN parapheur_bordereau BYTEA DEFAULT NULL;

-- Table Nomenclatures
DROP TABLE  IF EXISTS nomenclatures;
CREATE TABLE nomenclatures (
  id        VARCHAR PRIMARY KEY         NOT NULL,
  parent_id VARCHAR                     NOT NULL DEFAULT 0,
  libelle   VARCHAR                     NOT NULL,
  lft       INTEGER DEFAULT 0,
  rght      INTEGER DEFAULT 0,
  created   TIMESTAMP WITHOUT TIME ZONE NOT NULL,
  modified  TIMESTAMP WITHOUT TIME ZONE NOT NULL
);

ALTER TABLE tdt_messages ADD COLUMN date_message DATE DEFAULT NULL;
ALTER TABLE tdt_messages ADD COLUMN data BYTEA DEFAULT NULL;
ALTER TABLE tdt_messages RENAME COLUMN reponse TO type_reponse;
ALTER TABLE tdt_messages ALTER COLUMN type_reponse DROP NOT NULL;

ALTER TABLE typeactes ADD COLUMN gabarit_acte_name VARCHAR DEFAULT NULL;
ALTER TABLE typeactes ADD COLUMN gabarit_projet_name VARCHAR DEFAULT NULL;
ALTER TABLE typeactes ADD COLUMN gabarit_synthese_name VARCHAR DEFAULT NULL;

ALTER TABLE seances ADD COLUMN idelibre_id VARCHAR DEFAULT NULL;

ALTER TABLE deliberations ALTER COLUMN signee SET DEFAULT FALSE;
UPDATE deliberations SET signee=FALSE WHERE signee IS NULL;
ALTER TABLE deliberations ALTER COLUMN signee SET NOT NULL;
ALTER TABLE historiques ALTER COLUMN circuit_id DROP NOT NULL;

ALTER TABLE users ADD theme VARCHAR DEFAULT NULL,;

COMMIT;