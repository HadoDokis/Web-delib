UPDATE aros 
SET model = 'User'
WHERE model= 'Utilisateur' ;

UPDATE "wkf_traitements"  set treated = 1
WHERE target_id IN ( SELECT deliberations.id FROM deliberations WHERE etat >=2) 
AND     wkf_traitements.treated = 0 ;

ALTER TABLE "users_services" DROP INDEX users ADD INDEX users_services_users ( user_id ) ;

ALTER TABLE "wkf_visas" DROP INDEX traitements_users ,
ADD INDEX wkf_visas_traitements_users ( traitement_id , trigger_id ) ; 

ALTER TABLE "wkf_visas" DROP INDEX trigger ,
ADD INDEX wkf_visas_trigger ( "trigger_id" ) ;

ALTER TABLE "wkf_visas" DROP INDEX traitements ,
ADD INDEX wkf_visas_traitements ( traitement_id ) ;

ALTER TABLE acteurs ADD  COLUMN  suppleant_id INT;

CREATE TABLE typeactes (
    id integer NOT NULL,
    libelle text NOT NULL,
    modeleprojet_id integer NOT NULL,
    modelefinal_id integer NOT NULL,
    nature_id integer NOT NULL,
    compteur_id integer NOT NULL,
    created date NOT NULL,
    modified date NOT NULL
);
ALTER TABLE public.typeactes OWNER TO webdelib;
CREATE SEQUENCE typeactes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE public.typeactes_id_seq OWNER TO webdelib;
ALTER SEQUENCE typeactes_id_seq OWNED BY typeactes.id;
ALTER TABLE ONLY typeactes ALTER COLUMN id SET DEFAULT nextval('typeactes_id_seq'::regclass);
ALTER TABLE ONLY typeactes ADD CONSTRAINT typeactes_id_key UNIQUE (id);


BEGIN;
    ALTER TABLE wkf_traitements ADD COLUMN treated_b boolean;
    UPDATE wkf_traitements 
    SET treated_b = CASE WHEN treated=0 
                         THEN false 
                         ELSE true END;
    ALTER TABLE wkf_traitements RENAME COLUMN treated TO treated_orig;
    ALTER TABLE wkf_traitements RENAME COLUMN treated_b TO treated;
COMMIT;

BEGIN;
CREATE TABLE deliberations_typeseances (
    id        INTEGER CONSTRAINT deliberations_typeseances_pkey PRIMARY KEY,
    deliberation_id       INTEGER NOT NULL,
    typeseance_id         INTEGER NOT NULL
);

CREATE SEQUENCE "deliberations_typeseances_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE "deliberations_typeseances_id_seq" OWNED BY "deliberations_typeseances".id;
ALTER TABLE "deliberations_typeseances" ALTER COLUMN id SET DEFAULT nextval('"deliberations_typeseances_id_seq"'::regclass);
COMMIT;

BEGIN;
CREATE TABLE circuits_users (
    id        INTEGER CONSTRAINT circuits_users_pkey PRIMARY KEY,
    circuit_id       INTEGER NOT NULL,
    user_id         INTEGER NOT NULL
);

CREATE SEQUENCE "circuits_users_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE "circuits_users_id_seq" OWNED BY "circuits_users".id;
ALTER TABLE "circuits_users" ALTER COLUMN id SET DEFAULT nextval('"circuits_users_id_seq"'::regclass);
COMMIT;


BEGIN;
CREATE TABLE acteurs_seances (
    id         INTEGER CONSTRAINT acteurs_seances_pkey PRIMARY KEY,
    acteur_id  INTEGER NOT NULL,
    seance_id  INTEGER NOT NULL,
    mail_id    INTEGER NOT NULL,
    date_envoi  timestamp without time zone NOT NULL,
    date_reception  timestamp without time zone NULL
);

CREATE SEQUENCE "acteurs_seances_id_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER SEQUENCE "acteurs_seances_id_seq" OWNED BY "acteurs_seances".id;
ALTER TABLE "acteurs_seances" ALTER COLUMN id SET DEFAULT nextval('"acteurs_seances_id_seq"'::regclass);

COMMIT;
ALTER TABLE deliberations ADD COLUMN date_acte timestamp without time zone ;

CREATE INDEX deliberations_id ON deliberations_seances (deliberation_id);
CREATE INDEX seances_id ON deliberations_seances (seance_id);
ALTER TABLE deliberations RENAME COLUMN nature_id to typeacte_id;

ALTER TABLE typeseances_natures RENAME TO typeseances_typeactes;
ALTER TABLE typeseances_typeactes RENAME COLUMN nature_id to typeacte_id;

ALTER TABLE users   ALTER COLUMN note TYPE varchar(300);
ALTER TABLE seances  ALTER COLUMN  debat_global DROP NOT NULL;


ALTER TABLE seances  ALTER COLUMN  debat_global_name DROP NOT NULL;
ALTER TABLE seances  ALTER COLUMN  debat_global_type DROP NOT NULL;
ALTER TABLE seances  ALTER COLUMN  debat_global_size DROP NOT NULL;

ALTER TABLE models  ALTER COLUMN  modele DROP NOT NULL;
ALTER TABLE models  ALTER COLUMN  type DROP NOT NULL;
ALTER TABLE models  ALTER COLUMN  size DROP NOT NULL;
ALTER TABLE models  ALTER COLUMN  content DROP NOT NULL;
ALTER TABLE models  ALTER COLUMN  joindre_annexe DROP NOT NULL;

ALTER TABLE deliberations  ALTER COLUMN  anterieure_id DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  num_delib DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  montant DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  debat DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  debat_size DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  debat_type DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  debat_name DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  avis DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  vote_nb_oui DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  vote_nb_non DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  vote_nb_abstention DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  vote_nb_retrait DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  vote_commentaire DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  commission DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  commission_size DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  commission_name DROP NOT NULL;
ALTER TABLE deliberations  ALTER COLUMN  commission_type DROP NOT NULL;

ALTER TABLE deliberations_seances ADD COLUMN avis boolean;
ALTER TABLE deliberations_seances  ALTER COLUMN  avis  DROP NOT NULL;

ALTER TABLE infosups ALTER COLUMN text  DROP NOT NULL;
ALTER TABLE infosups ALTER COLUMN date  DROP NOT NULL;
ALTER TABLE infosups ALTER COLUMN file_name  DROP NOT NULL;
ALTER TABLE infosups ALTER COLUMN file_size  DROP NOT NULL;
ALTER TABLE infosups ALTER COLUMN file_type  DROP NOT NULL;
ALTER TABLE infosups ALTER COLUMN content  DROP NOT NULL;
ALTER TABLE infosups ALTER COLUMN infosupdef_id DROP NOT NULL;

ALTER TABLE wkf_circuits  ALTER COLUMN created_user_id DROP NOT NULL;
ALTER TABLE commentaires  ALTER COLUMN commentaire_auto DROP NOT NULL;
ALTER TABLE annexes  ALTER COLUMN filename_pdf DROP NOT NULL;
ALTER TABLE annexes  ALTER COLUMN data_pdf DROP NOT NULL;

ALTER TABLE compteurs ALTER COLUMN val_reinit DROP NOT NULL;

ALTER TABLE themes ALTER COLUMN parent_id DROP NOT NULL;
ALTER TABLE services ALTER COLUMN parent_id DROP NOT NULL;

INSERT INTO "circuits_users" (user_id, circuit_id)  select users.id, "wkf_circuits".id from users, "wkf_circuits";

