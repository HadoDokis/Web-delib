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

-- Nouvelles notifications utilisateur
ALTER TABLE users ADD COLUMN mail_modif_projet_cree BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN mail_modif_projet_valide BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN mail_retard_validation BOOLEAN DEFAULT FALSE;

-- Mise à jour : joindre les annexes par défaut
UPDATE annexes SET joindre_fusion=1;

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
UPDATE crons set action='delegationJob', plugin=null WHERE id=1;

-- Tâche planifiée de déclenchement des alertes de retard (workflow)
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
  VALUES ('2', 'Circuits de traitement : Déclenchement des alertes de retard', 'Relance les utilisateurs devant valider un projet en retard', null, 'CronJob',	'retardCakeflowJob',	'f', null, now(), 'P1D', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', null,	true, now(), '1', now(), '1');

-- Tâche planifiée de mise à jour du status des actes envoyés au i-parapheur
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
  VALUES ('3', 'Signature : Mise à jour de l''etat des dossiers envoyés au i-Parapheur', 'Met à jour l''état des projets envoyés au i-Parapheur pour signature et rapatrie les informations de ceux en fin de circuit.', null, 'CronJob',	'signatureJob',	'f', null, now(), 'P1D', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', null,	true, now(), '1', now(), '1');

-- Tâche planifiée de mise à jour du status des actes envoyés au i-parapheur
INSERT INTO crons (id, nom, description, plugin, model, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id)
  VALUES ('4', 'S2low : mise à jours des échanges', 'Envoi/Réception des courriers S2low', null, 'CronJob',	's2lowJob',	'f', null, now(), 'P1D', now(), now(), 'Cette tâche n''a encore jamais été exécutée.', null,	true, now(), '1', now(), '1');

ALTER TABLE crons ADD COLUMN lock BOOL DEFAULT FALSE;

-- Mise à jour de la table models
ALTER TABLE deliberations RENAME COLUMN etat_asalae TO sae_etat;
ALTER TABLE deliberations RENAME COLUMN etat_parapheur TO parapheur_etat;
ALTER TABLE deliberations RENAME COLUMN id_parapheur TO parapheur_id;
ALTER TABLE deliberations RENAME COLUMN commentaire_refus_parapheur TO parapheur_commentaire;
ALTER TABLE deliberations RENAME COLUMN "dateAR" TO "tdt_dateAR";
ALTER TABLE deliberations ADD COLUMN parapheur_cible VARCHAR DEFAULT NULL;

COMMIT;