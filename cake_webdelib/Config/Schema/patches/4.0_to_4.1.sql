ALTER TABLE users ADD COLUMN zone_1 character varying(50);
ALTER TABLE users ADD COLUMN zone_2 character varying(50);
ALTER TABLE users ADD COLUMN zone_3 character varying(50);
ALTER TABLE users ADD COLUMN zone_4 character varying(50);
ALTER TABLE users ADD COLUMN zone_5 character varying(50);
ALTER TABLE users ADD COLUMN zone_6 character varying(50);
ALTER TABLE users ADD COLUMN zone_7 character varying(50);
ALTER TABLE users ADD COLUMN zone_8 character varying(50);
ALTER TABLE users ADD COLUMN zone_9 character varying(50);

CREATE TABLE infosupdefs_profils (
    id integer NOT NULL,
    profil_id integer NOT NULL,
    infosupdef_id integer NOT NULL
);
ALTER TABLE public.infosupdefs_profils OWNER TO webdelib;
CREATE SEQUENCE infosupdefs_profils_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE public.infosupdefs_profils_id_seq OWNER TO webdelib;
ALTER SEQUENCE infosupdefs_profils_id_seq OWNED BY infosupdefs_profils.id;
ALTER TABLE infosupdefs_profils ALTER COLUMN id  SET DEFAULT NEXTVAL('infosupdefs_profils_id_seq');
INSERT INTO "infosupdefs_profils" (profil_id, infosupdef_id) select profils.id, infosupdefs.id from profils, infosupdefs;

ALTER TABLE annexes   ALTER COLUMN titre TYPE varchar(200);

ALTER TABLE infosupdefs ADD COLUMN actif boolean;
UPDATE infosupdefs set actif = true;

ALTER TABLE historiques ALTER COLUMN commentaire TYPE TEXT;

--Crons
CREATE TABLE crons (
    id SERIAL NOT NULL, 
    nom varchar(255) NOT NULL, 
    description varchar(255) DEFAULT NULL::character varying , 
    plugin varchar(255) DEFAULT '', 
    controller varchar(255) NOT NULL, 
    "action" varchar(255) NOT NULL, 
    has_params boolean, 
    params varchar(255) DEFAULT NULL::character varying , 
    next_execution_time timestamp, 
    execution_duration varchar(255), 
    last_execution_start_time timestamp, 
    last_execution_end_time timestamp, 
    last_execution_report text, 
    last_execution_status varchar(255), 
    active boolean, 
    created timestamp NOT NULL,
    created_user_id int4 NOT NULL, 
    modified timestamp NOT NULL, 
    modified_user_id int4 NOT NULL
);

ALTER TABLE public.crons OWNER TO webdelib;

INSERT INTO "public".crons (nom, description, plugin, controller, "action", has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id) 
	VALUES ('Circuits de traitement : Mise à jour des traitements extérieurs', 'Lecture de l''état des traitements extérieurs (iParapheur)', 'cakeflow', 'traitements', 'majTraitementsParapheur', false, '', '2013-03-14 17:45:00.0', 'PT1H', '2013-03-14 17:10:03.0', '2013-03-14 17:10:03.0', '', 'SUCCES', true, '2013-03-06 11:01:46.996708', 1, '2013-03-14 11:44:45.0', 2);

ALTER TABLE deliberations ADD COLUMN date_envoi_signature timestamp without time zone NULL;


