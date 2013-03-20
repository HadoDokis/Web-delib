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
