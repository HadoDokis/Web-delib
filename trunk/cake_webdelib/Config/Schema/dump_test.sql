

DROP TABLE "public"."acos";
DROP TABLE "public"."acteurs";
DROP TABLE "public"."acteurs_seances";
DROP TABLE "public"."acteurs_services";
DROP TABLE "public"."ados";
DROP TABLE "public"."annexes";
DROP TABLE "public"."aros";
DROP TABLE "public"."aros_acos";
DROP TABLE "public"."aros_ados";
DROP TABLE "public"."circuits_users";
DROP TABLE "public"."collectivites";
DROP TABLE "public"."commentaires";
DROP TABLE "public"."compteurs";
DROP TABLE "public"."crons";
DROP TABLE "public"."deliberations";
DROP TABLE "public"."deliberations_seances";
DROP TABLE "public"."deliberations_typeseances";
DROP TABLE "public"."historiques";
DROP TABLE "public"."infosupdefs";
DROP TABLE "public"."infosupdefs_profils";
DROP TABLE "public"."infosuplistedefs";
DROP TABLE "public"."infosups";
DROP TABLE "public"."listepresences";
DROP TABLE "public"."models";
DROP TABLE "public"."natures";
DROP TABLE "public"."nomenclatures";
DROP TABLE "public"."profils";
DROP TABLE "public"."seances";
DROP TABLE "public"."sequences";
DROP TABLE "public"."services";
DROP TABLE "public"."tdt_messages";
DROP TABLE "public"."themes";
DROP TABLE "public"."typeactes";
DROP TABLE "public"."typeacteurs";
DROP TABLE "public"."typeseances";
DROP TABLE "public"."typeseances_acteurs";
DROP TABLE "public"."typeseances_typeactes";
DROP TABLE "public"."typeseances_typeacteurs";
DROP TABLE "public"."users";
DROP TABLE "public"."users_services";
DROP TABLE "public"."votes";


CREATE TABLE "public"."acos" (
	"id" serial NOT NULL,
	"alias" varchar(255) NOT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	"parent_id" integer DEFAULT 0,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX acos_idx1 ON "public"."acos"("lft", "rght");
CREATE INDEX acos_idx2 ON "public"."acos"("alias");
CREATE INDEX acos_idx3 ON "public"."acos"("model", "foreign_key");
CREATE INDEX acos_leftright ON "public"."acos"("lft", "rght");
CREATE INDEX lft ON "public"."acos"("lft");

CREATE TABLE "public"."acteurs" (
	"id" serial NOT NULL,
	"typeacteur_id" integer DEFAULT 0 NOT NULL,
	"nom" varchar(50) NOT NULL,
	"prenom" varchar(50) NOT NULL,
	"salutation" varchar(50) NOT NULL,
	"titre" varchar(250) DEFAULT NULL,
	"position" integer NOT NULL,
	"date_naissance" date DEFAULT NULL,
	"adresse1" varchar(100) NOT NULL,
	"adresse2" varchar(100) NOT NULL,
	"cp" varchar(20) NOT NULL,
	"ville" varchar(100) NOT NULL,
	"email" varchar(100) NOT NULL,
	"telfixe" varchar(20) DEFAULT NULL,
	"telmobile" varchar(20) DEFAULT NULL,
	"suppleant_id" integer DEFAULT NULL,
	"note" varchar(255) NOT NULL,
	"actif" boolean DEFAULT 'TRUE' NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX acteurs_actif ON "public"."acteurs"("actif");
CREATE INDEX index ON "public"."acteurs"("id");
CREATE INDEX suppleant_id ON "public"."acteurs"("suppleant_id");
CREATE INDEX typeacteur ON "public"."acteurs"("typeacteur_id");

CREATE TABLE "public"."acteurs_seances" (
	"id" serial NOT NULL,
	"acteur_id" integer NOT NULL,
	"seance_id" integer NOT NULL,
	"mail_id" integer NOT NULL,
	"date_envoi" timestamp NOT NULL,
	"date_reception" timestamp ,
	"model" varchar(20) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."acteurs_services" (
	"id" serial NOT NULL,
	"acteur_id" integer DEFAULT 0 NOT NULL,
	"service_id" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX acteursservices_acteur_id ON "public"."acteurs_services"("acteur_id");
CREATE INDEX acteursservices_service_id ON "public"."acteurs_services"("service_id");

CREATE TABLE "public"."ados" (
	"id" serial NOT NULL,
	"alias" varchar(255) NOT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	"parent_id" integer DEFAULT 0 NOT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."annexes" (
	"id" serial NOT NULL,
	"model" varchar(255) NOT NULL,
	"foreign_key" integer NOT NULL,
	"joindre_ctrl_legalite" boolean NOT NULL,
	"joindre_fusion" boolean NOT NULL,
	"titre" varchar(200) NOT NULL,
	"filename" varchar(75) NOT NULL,
	"filetype" varchar(255) NOT NULL,
	"size" integer NOT NULL,
	"data" bytea NOT NULL,
	"filename_pdf" varchar(75) DEFAULT NULL,
	"data_pdf" bytea DEFAULT NULL,
	"created" timestamp ,
	"modified" timestamp ,
	PRIMARY KEY  ("id")
);
CREATE INDEX annexes_joindre ON "public"."annexes"("foreign_key", "joindre_fusion");
CREATE INDEX model_foreign_key ON "public"."annexes"("model", "foreign_key");

CREATE TABLE "public"."aros" (
	"id" serial NOT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) NOT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	"parent_id" integer DEFAULT 0,
	"model" varchar(255) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX alias ON "public"."aros"("alias");
CREATE INDEX alias_2 ON "public"."aros"("alias");
CREATE INDEX aros_idx1 ON "public"."aros"("lft", "rght");
CREATE INDEX aros_idx2 ON "public"."aros"("alias");
CREATE INDEX aros_idx3 ON "public"."aros"("model", "foreign_key");
CREATE INDEX aros_leftright ON "public"."aros"("lft", "rght");
CREATE INDEX parent_id ON "public"."aros"("parent_id");
CREATE INDEX rght ON "public"."aros"("rght");

CREATE TABLE "public"."aros_acos" (
	"id" serial NOT NULL,
	"aro_id" integer NOT NULL,
	"aco_id" integer NOT NULL,
	"_create" varchar(2) DEFAULT '0' NOT NULL,
	"_read" varchar(2) DEFAULT '0' NOT NULL,
	"_update" varchar(2) DEFAULT '0' NOT NULL,
	"_delete" varchar(2) DEFAULT '0' NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX aco_id ON "public"."aros_acos"("aco_id");

CREATE TABLE "public"."aros_ados" (
	"id" serial NOT NULL,
	"aro_id" integer NOT NULL,
	"ado_id" integer NOT NULL,
	"_create" varchar(2) DEFAULT '0' NOT NULL,
	"_read" varchar(2) DEFAULT '0' NOT NULL,
	"_update" varchar(2) DEFAULT '0' NOT NULL,
	"_delete" varchar(2) DEFAULT '0' NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX ado_id ON "public"."aros_ados"("ado_id");
CREATE INDEX aro_id ON "public"."aros_ados"("aro_id");

CREATE TABLE "public"."circuits_users" (
	"id" serial NOT NULL,
	"circuit_id" integer NOT NULL,
	"user_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX user_id ON "public"."circuits_users"("user_id");

CREATE TABLE "public"."collectivites" (
	"id" serial NOT NULL,
	"id_entity" integer DEFAULT NULL,
	"nom" varchar(30) NOT NULL,
	"adresse" varchar(255) NOT NULL,
	"CP" integer NOT NULL,
	"ville" varchar(255) NOT NULL,
	"telephone" varchar(20) NOT NULL,
	"logo" bytea DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."commentaires" (
	"id" serial NOT NULL,
	"delib_id" integer DEFAULT 0 NOT NULL,
	"agent_id" integer DEFAULT 0 NOT NULL,
	"texte" varchar(1000) DEFAULT NULL,
	"pris_en_compte" integer DEFAULT 0 NOT NULL,
	"commentaire_auto" boolean DEFAULT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."compteurs" (
	"id" serial NOT NULL,
	"nom" varchar(255) NOT NULL,
	"commentaire" varchar(255) NOT NULL,
	"def_compteur" varchar(255) NOT NULL,
	"sequence_id" integer NOT NULL,
	"def_reinit" varchar(255) NOT NULL,
	"val_reinit" varchar(255) DEFAULT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."crons" (
	"id" serial NOT NULL,
	"nom" varchar(255) NOT NULL,
	"description" varchar(255) DEFAULT NULL,
	"plugin" varchar(255) DEFAULT NULL,
	"controller" varchar(255) NOT NULL,
	"action" varchar(255) NOT NULL,
	"has_params" boolean DEFAULT NULL,
	"params" varchar(255) DEFAULT NULL,
	"next_execution_time" timestamp ,
	"execution_duration" varchar(255) DEFAULT NULL,
	"last_execution_start_time" timestamp ,
	"last_execution_end_time" timestamp ,
	"last_execution_report" text DEFAULT NULL,
	"last_execution_status" varchar(255) DEFAULT NULL,
	"active" boolean DEFAULT NULL,
	"created" timestamp NOT NULL,
	"created_user_id" integer NOT NULL,
	"modified" timestamp NOT NULL,
	"modified_user_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."deliberations" (
	"id" serial NOT NULL,
	"typeacte_id" integer DEFAULT 1 NOT NULL,
	"circuit_id" integer DEFAULT 0,
	"theme_id" integer DEFAULT 0 NOT NULL,
	"service_id" integer DEFAULT 0 NOT NULL,
	"redacteur_id" integer DEFAULT 0 NOT NULL,
	"rapporteur_id" integer DEFAULT 0,
	"anterieure_id" integer DEFAULT NULL,
	"is_multidelib" boolean DEFAULT NULL,
	"parent_id" integer DEFAULT NULL,
	"objet" varchar(1000) NOT NULL,
	"objet_delib" varchar(1000) DEFAULT NULL,
	"titre" varchar(1000) DEFAULT NULL,
	"num_delib" varchar(15) DEFAULT NULL,
	"num_pref" varchar(255) NOT NULL,
	"pastell_id" varchar(10) DEFAULT NULL,
	"tdt_id" integer DEFAULT NULL,
	"dateAR" varchar(100) DEFAULT NULL,
	"texte_projet" bytea DEFAULT NULL,
	"texte_projet_name" varchar(75) DEFAULT NULL,
	"texte_projet_type" varchar(255) DEFAULT NULL,
	"texte_projet_size" integer DEFAULT NULL,
	"texte_synthese" bytea DEFAULT NULL,
	"texte_synthese_name" varchar(75) DEFAULT NULL,
	"texte_synthese_type" varchar(255) DEFAULT NULL,
	"texte_synthese_size" integer DEFAULT NULL,
	"deliberation" bytea DEFAULT NULL,
	"deliberation_name" varchar(75) DEFAULT NULL,
	"deliberation_type" varchar(255) DEFAULT NULL,
	"deliberation_size" integer DEFAULT NULL,
	"date_limite" date DEFAULT NULL,
	"date_envoi" timestamp ,
	"etat" integer DEFAULT 0 NOT NULL,
	"etat_parapheur" integer DEFAULT NULL,
	"commentaire_refus_parapheur" varchar(1000) DEFAULT NULL,
	"etat_asalae" boolean DEFAULT NULL,
	"reporte" boolean NOT NULL,
	"montant" integer DEFAULT NULL,
	"debat" bytea DEFAULT NULL,
	"debat_name" varchar(255) DEFAULT NULL,
	"debat_size" integer DEFAULT NULL,
	"debat_type" varchar(255) DEFAULT NULL,
	"avis" integer DEFAULT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"vote_nb_oui" integer DEFAULT NULL,
	"vote_nb_non" integer DEFAULT NULL,
	"vote_nb_abstention" integer DEFAULT NULL,
	"vote_nb_retrait" integer DEFAULT NULL,
	"vote_commentaire" varchar(500) DEFAULT NULL,
	"delib_pdf" bytea DEFAULT NULL,
	"bordereau" bytea DEFAULT NULL,
	"signature" bytea DEFAULT NULL,
	"signee" boolean DEFAULT NULL,
	"commission" bytea DEFAULT NULL,
	"commission_size" integer DEFAULT NULL,
	"commission_type" varchar(255) DEFAULT NULL,
	"commission_name" varchar(255) DEFAULT NULL,
	"date_acte" timestamp ,
	"date_envoi_signature" timestamp ,
	"id_parapheur" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX etat ON "public"."deliberations"("etat");
CREATE INDEX nature_id ON "public"."deliberations"("typeacte_id");
CREATE INDEX parent ON "public"."deliberations"("parent_id");
CREATE INDEX rapporteur_id ON "public"."deliberations"("rapporteur_id");
CREATE INDEX redacteur_id ON "public"."deliberations"("redacteur_id");
CREATE INDEX service_id ON "public"."deliberations"("service_id");
CREATE INDEX theme_id ON "public"."deliberations"("theme_id");

CREATE TABLE "public"."deliberations_seances" (
	"id" serial NOT NULL,
	"deliberation_id" integer NOT NULL,
	"seance_id" integer NOT NULL,
	"position" integer DEFAULT NULL,
	"avis" boolean DEFAULT NULL,
	"commentaire" varchar(1000) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX deliberations_id ON "public"."deliberations_seances"("deliberation_id");
CREATE INDEX deliberations_seances_seance ON "public"."deliberations_seances"("seance_id");
CREATE INDEX seances_id ON "public"."deliberations_seances"("seance_id");

CREATE TABLE "public"."deliberations_typeseances" (
	"id" serial NOT NULL,
	"deliberation_id" integer NOT NULL,
	"typeseance_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."historiques" (
	"id" serial NOT NULL,
	"delib_id" integer NOT NULL,
	"user_id" integer NOT NULL,
	"circuit_id" integer NOT NULL,
	"commentaire" text NOT NULL,
	"modified" timestamp NOT NULL,
	"created" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX user ON "public"."historiques"("user_id");

CREATE TABLE "public"."infosupdefs" (
	"id" serial NOT NULL,
	"model" varchar(25) DEFAULT 'Deliberation' NOT NULL,
	"nom" varchar(255) NOT NULL,
	"commentaire" varchar(255) NOT NULL,
	"ordre" integer NOT NULL,
	"code" varchar(255) NOT NULL,
	"type" varchar(255) NOT NULL,
	"val_initiale" varchar(1000) DEFAULT NULL,
	"recherche" boolean NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"actif" boolean DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX model ON "public"."infosupdefs"("model");

CREATE TABLE "public"."infosupdefs_profils" (
	"id" serial NOT NULL,
	"profil_id" integer NOT NULL,
	"infosupdef_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."infosuplistedefs" (
	"id" serial NOT NULL,
	"infosupdef_id" integer NOT NULL,
	"ordre" integer NOT NULL,
	"nom" varchar(255) NOT NULL,
	"actif" boolean DEFAULT 'TRUE' NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX INFOSUPDEF_ID_ORDRE ON "public"."infosuplistedefs"("infosupdef_id", "ordre");

CREATE TABLE "public"."infosups" (
	"id" serial NOT NULL,
	"model" varchar(25) DEFAULT 'Deliberation' NOT NULL,
	"foreign_key" integer NOT NULL,
	"infosupdef_id" integer DEFAULT NULL,
	"text" varchar(255) DEFAULT NULL,
	"date" date DEFAULT NULL,
	"file_name" varchar(255) DEFAULT NULL,
	"file_size" integer DEFAULT NULL,
	"file_type" varchar(255) DEFAULT NULL,
	"content" bytea DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX foreign_key ON "public"."infosups"("foreign_key");
CREATE INDEX infosupdef_id ON "public"."infosups"("infosupdef_id");
CREATE INDEX text ON "public"."infosups"("text");

CREATE TABLE "public"."listepresences" (
	"id" serial NOT NULL,
	"delib_id" integer NOT NULL,
	"acteur_id" integer NOT NULL,
	"present" boolean NOT NULL,
	"mandataire" integer DEFAULT NULL,
	"suppleant_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."models" (
	"id" serial NOT NULL,
	"modele" varchar(100) DEFAULT NULL,
	"type" varchar(100) DEFAULT NULL,
	"name" varchar(255) DEFAULT NULL,
	"size" integer DEFAULT NULL,
	"extension" varchar(255) DEFAULT NULL,
	"content" bytea DEFAULT NULL,
	"recherche" boolean DEFAULT NULL,
	"joindre_annexe" integer DEFAULT 0,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"multiodj" boolean DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."natures" (
	"id" serial NOT NULL,
	"libelle" varchar(100) NOT NULL,
	"code" varchar(3) NOT NULL,
	"dua" varchar(50) DEFAULT NULL,
	"sortfinal" varchar(50) DEFAULT NULL,
	"communicabilite" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."nomenclatures" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT 0 NOT NULL,
	"libelle" varchar(100) NOT NULL,
	"code" varchar(50) DEFAULT '0',
	"lft" integer DEFAULT 0,
	"rght" integer DEFAULT 0,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."profils" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT 0,
	"libelle" varchar(100) NOT NULL,
	"actif" boolean DEFAULT 'TRUE' NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."seances" (
	"id" serial NOT NULL,
	"type_id" integer DEFAULT 0 NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"date_convocation" timestamp ,
	"date" timestamp NOT NULL,
	"traitee" integer DEFAULT 0 NOT NULL,
	"commentaire" varchar(500) DEFAULT NULL,
	"secretaire_id" integer DEFAULT NULL,
	"president_id" integer DEFAULT NULL,
	"debat_global" bytea DEFAULT NULL,
	"debat_global_name" varchar(75) DEFAULT NULL,
	"debat_global_size" integer DEFAULT NULL,
	"debat_global_type" varchar(255) DEFAULT NULL,
	"pv_figes" integer DEFAULT NULL,
	"pv_sommaire" bytea DEFAULT NULL,
	"pv_complet" bytea DEFAULT NULL,
	"numero_depot" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX seances_traitee ON "public"."seances"("traitee");
CREATE INDEX type_id ON "public"."seances"("type_id");

CREATE TABLE "public"."sequences" (
	"id" serial NOT NULL,
	"nom" varchar(255) NOT NULL,
	"commentaire" varchar(255) NOT NULL,
	"num_sequence" integer NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."services" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT 0,
	"order" varchar(50) NOT NULL,
	"libelle" varchar(100) NOT NULL,
	"circuit_defaut_id" integer NOT NULL,
	"actif" boolean DEFAULT 'TRUE' NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"lft" integer DEFAULT 0,
	"rght" integer DEFAULT 0,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."tdt_messages" (
	"id" serial NOT NULL,
	"delib_id" integer NOT NULL,
	"message_id" integer NOT NULL,
	"type_message" integer NOT NULL,
	"reponse" integer NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX tdtmsg_ ON "public"."tdt_messages"("delib_id");

CREATE TABLE "public"."themes" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT 0,
	"order" varchar(50) NOT NULL,
	"libelle" varchar(500) DEFAULT NULL,
	"actif" boolean DEFAULT 'TRUE' NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"lft" integer DEFAULT 0,
	"rght" integer DEFAULT 0,
	PRIMARY KEY  ("id")
);
CREATE INDEX themes_left ON "public"."themes"("lft");

CREATE TABLE "public"."typeactes" (
	"id" serial NOT NULL,
	"libelle" text NOT NULL,
	"modeleprojet_id" integer NOT NULL,
	"modelefinal_id" integer NOT NULL,
	"nature_id" integer NOT NULL,
	"compteur_id" integer NOT NULL,
	"created" date NOT NULL,
	"modified" date NOT NULL
);
CREATE UNIQUE INDEX typeactes_id_key ON "public"."typeactes"("id");

CREATE TABLE "public"."typeacteurs" (
	"id" serial NOT NULL,
	"nom" varchar(255) NOT NULL,
	"commentaire" varchar(255) NOT NULL,
	"elu" boolean NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX elu ON "public"."typeacteurs"("elu");

CREATE TABLE "public"."typeseances" (
	"id" serial NOT NULL,
	"libelle" varchar(100) NOT NULL,
	"retard" integer DEFAULT 0,
	"action" integer NOT NULL,
	"compteur_id" integer NOT NULL,
	"modelprojet_id" integer NOT NULL,
	"modeldeliberation_id" integer NOT NULL,
	"modelconvocation_id" integer NOT NULL,
	"modelordredujour_id" integer NOT NULL,
	"modelpvsommaire_id" integer NOT NULL,
	"modelpvdetaille_id" integer NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."typeseances_acteurs" (
	"id" serial NOT NULL,
	"typeseance_id" integer NOT NULL,
	"acteur_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."typeseances_typeactes" (
	"id" serial NOT NULL,
	"typeseance_id" integer NOT NULL,
	"typeacte_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX typeseancenature_nature ON "public"."typeseances_typeactes"("typeacte_id");

CREATE TABLE "public"."typeseances_typeacteurs" (
	"id" serial NOT NULL,
	"typeseance_id" integer NOT NULL,
	"typeacteur_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX typeseance_id ON "public"."typeseances_typeacteurs"("typeseance_id", "typeacteur_id");

CREATE TABLE "public"."users" (
	"id" serial NOT NULL,
	"profil_id" integer DEFAULT 0 NOT NULL,
	"statut" integer DEFAULT 0 NOT NULL,
	"login" varchar(50) NOT NULL,
	"note" varchar(300) DEFAULT NULL,
	"circuit_defaut_id" integer DEFAULT NULL,
	"password" varchar(100) NOT NULL,
	"nom" varchar(50) NOT NULL,
	"prenom" varchar(50) NOT NULL,
	"email" varchar(255) NOT NULL,
	"telfixe" varchar(20) DEFAULT NULL,
	"telmobile" varchar(20) DEFAULT NULL,
	"date_naissance" date DEFAULT NULL,
	"accept_notif" boolean DEFAULT NULL,
	"mail_refus" boolean NOT NULL,
	"mail_traitement" boolean NOT NULL,
	"mail_insertion" boolean NOT NULL,
	"position" integer DEFAULT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX login ON "public"."users"("login");

CREATE TABLE "public"."users_services" (
	"id" serial NOT NULL,
	"user_id" integer DEFAULT 0 NOT NULL,
	"service_id" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX users_services_users ON "public"."users_services"("user_id");

CREATE TABLE "public"."votes" (
	"id" serial NOT NULL,
	"acteur_id" integer DEFAULT 0 NOT NULL,
	"delib_id" integer DEFAULT 0 NOT NULL,
	"resultat" integer DEFAULT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX acteur_id ON "public"."votes"("acteur_id");
CREATE INDEX deliberation_id ON "public"."votes"("delib_id");

