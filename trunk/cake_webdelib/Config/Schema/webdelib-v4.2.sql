--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: acos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE acos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: acos; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE acos (
    id integer DEFAULT nextval('acos_id_seq'::regclass) NOT NULL,
    alias character varying(255) DEFAULT ''::character varying NOT NULL,
    lft integer,
    rght integer,
    parent_id integer DEFAULT 0,
    model character varying(255),
    foreign_key integer DEFAULT 0 NOT NULL
);


--
-- Name: acteurs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE acteurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: acteurs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE acteurs (
    id integer DEFAULT nextval('acteurs_id_seq'::regclass) NOT NULL,
    typeacteur_id integer DEFAULT 0 NOT NULL,
    nom character varying(50) DEFAULT ''::character varying NOT NULL,
    prenom character varying(50) DEFAULT ''::character varying NOT NULL,
    salutation character varying(50) NOT NULL,
    titre character varying(250),
    "position" integer NOT NULL,
    date_naissance date,
    adresse1 character varying(100) NOT NULL,
    adresse2 character varying(100) NOT NULL,
    cp character varying(20) NOT NULL,
    ville character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    telfixe character varying(20),
    telmobile character varying(20),
    suppleant_id integer,
    note character varying(255) NOT NULL,
    actif boolean DEFAULT true NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: acteurs_seances; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE acteurs_seances (
    id integer NOT NULL,
    acteur_id integer NOT NULL,
    seance_id integer NOT NULL,
    mail_id integer NOT NULL,
    date_envoi timestamp without time zone NOT NULL,
    date_reception timestamp without time zone,
    model character varying(20) NOT NULL
);


--
-- Name: acteurs_seances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE acteurs_seances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: acteurs_seances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE acteurs_seances_id_seq OWNED BY acteurs_seances.id;


--
-- Name: acteurs_services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE acteurs_services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: acteurs_services; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE acteurs_services (
    id integer DEFAULT nextval('acteurs_services_id_seq'::regclass) NOT NULL,
    acteur_id integer DEFAULT 0 NOT NULL,
    service_id integer DEFAULT 0 NOT NULL
);


--
-- Name: ados_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE ados_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ados; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ados (
    id integer DEFAULT nextval('ados_id_seq'::regclass) NOT NULL,
    alias character varying(255) DEFAULT ''::character varying NOT NULL,
    lft integer,
    rght integer,
    parent_id integer DEFAULT 0 NOT NULL,
    model character varying(255),
    foreign_key integer DEFAULT 0 NOT NULL
);


--
-- Name: annexes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE annexes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: annexes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE annexes (
    id integer DEFAULT nextval('annexes_id_seq'::regclass) NOT NULL,
    model character varying(255) NOT NULL,
    foreign_key integer NOT NULL,
    joindre_ctrl_legalite boolean DEFAULT false NOT NULL,
    joindre_fusion boolean DEFAULT false NOT NULL,
    titre character varying(200) NOT NULL,
    filename character varying(100) NOT NULL,
    filetype character varying(255) NOT NULL,
    size integer NOT NULL,
    data bytea NOT NULL,
    filename_pdf character varying(75),
    data_pdf bytea,
    created timestamp without time zone,
    modified timestamp without time zone,
    edition_data bytea,
    edition_data_typemime character varying
);


--
-- Name: aros_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE aros_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: aros; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE aros (
    id integer DEFAULT nextval('aros_id_seq'::regclass) NOT NULL,
    foreign_key bigint,
    alias character varying(255) DEFAULT ''::character varying NOT NULL,
    lft integer,
    rght integer,
    parent_id integer DEFAULT 0,
    model character varying(255)
);


--
-- Name: aros_acos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE aros_acos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: aros_acos; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE aros_acos (
    id integer DEFAULT nextval('aros_acos_id_seq'::regclass) NOT NULL,
    aro_id bigint NOT NULL,
    aco_id bigint NOT NULL,
    _create character(2) DEFAULT '0'::character(1) NOT NULL,
    _read character(2) DEFAULT '0'::character(1) NOT NULL,
    _update character(2) DEFAULT '0'::character(1) NOT NULL,
    _delete character(2) DEFAULT '0'::character(1) NOT NULL
);


--
-- Name: aros_ados_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE aros_ados_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: aros_ados; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE aros_ados (
    id integer DEFAULT nextval('aros_ados_id_seq'::regclass) NOT NULL,
    aro_id bigint NOT NULL,
    ado_id bigint NOT NULL,
    _create character(2) DEFAULT '0'::character(1) NOT NULL,
    _read character(2) DEFAULT '0'::character(1) NOT NULL,
    _update character(2) DEFAULT '0'::character(1) NOT NULL,
    _delete character(2) DEFAULT '0'::character(1) NOT NULL
);


--
-- Name: circuits_users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE circuits_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: circuits_users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE circuits_users (
    id integer DEFAULT nextval('circuits_users_id_seq'::regclass) NOT NULL,
    circuit_id integer NOT NULL,
    user_id integer NOT NULL
);


--
-- Name: collectivites; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE collectivites (
    id integer NOT NULL,
    id_entity integer,
    nom character varying(30) NOT NULL,
    adresse character varying(255) NOT NULL,
    "CP" integer NOT NULL,
    ville character varying(255) NOT NULL,
    telephone character varying(20) NOT NULL,
    logo bytea
);


--
-- Name: commentaires_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE commentaires_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: commentaires; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE commentaires (
    id integer DEFAULT nextval('commentaires_id_seq'::regclass) NOT NULL,
    delib_id integer DEFAULT 0 NOT NULL,
    agent_id integer DEFAULT 0 NOT NULL,
    texte character varying(1000),
    pris_en_compte smallint DEFAULT 0 NOT NULL,
    commentaire_auto boolean,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: compteurs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE compteurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: compteurs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE compteurs (
    id integer DEFAULT nextval('compteurs_id_seq'::regclass) NOT NULL,
    nom character varying(255) NOT NULL,
    commentaire character varying(255) NOT NULL,
    def_compteur character varying(255) NOT NULL,
    sequence_id integer NOT NULL,
    def_reinit character varying(255) NOT NULL,
    val_reinit character varying(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: crons; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE crons (
    id integer NOT NULL,
    nom character varying(255) NOT NULL,
    description character varying(255) DEFAULT NULL::character varying,
    plugin character varying(255) DEFAULT ''::character varying,
    action character varying(255) NOT NULL,
    has_params boolean,
    params character varying(255) DEFAULT NULL::character varying,
    next_execution_time timestamp without time zone,
    execution_duration character varying(255),
    last_execution_start_time timestamp without time zone,
    last_execution_end_time timestamp without time zone,
    last_execution_report text,
    last_execution_status character varying(255),
    active boolean,
    created timestamp without time zone NOT NULL,
    created_user_id integer NOT NULL,
    modified timestamp without time zone NOT NULL,
    modified_user_id integer NOT NULL,
    model character varying DEFAULT 'CronJob'::character varying,
    lock boolean DEFAULT false
);


--
-- Name: crons_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE crons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: crons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE crons_id_seq OWNED BY crons.id;


--
-- Name: deliberations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE deliberations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: deliberations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE deliberations (
    id integer DEFAULT nextval('deliberations_id_seq'::regclass) NOT NULL,
    typeacte_id integer DEFAULT 1 NOT NULL,
    circuit_id integer DEFAULT 0,
    theme_id integer DEFAULT 0 NOT NULL,
    service_id integer DEFAULT 0 NOT NULL,
    redacteur_id integer DEFAULT 0 NOT NULL,
    rapporteur_id integer DEFAULT 0,
    anterieure_id integer,
    is_multidelib boolean,
    parent_id integer,
    objet character varying(1000) NOT NULL,
    objet_delib character varying(1000),
    titre character varying(1000),
    num_delib character varying(15),
    num_pref character varying(255),
    pastell_id character varying(10),
    tdt_id character varying,
    "tdt_dateAR" character varying(100),
    texte_projet bytea,
    texte_projet_name character varying(75),
    texte_projet_type character varying(255),
    texte_projet_size integer,
    texte_synthese bytea,
    texte_synthese_name character varying(75),
    texte_synthese_type character varying(255),
    texte_synthese_size integer,
    deliberation bytea,
    deliberation_name character varying(75),
    deliberation_type character varying(255),
    deliberation_size integer,
    date_limite date,
    date_envoi timestamp without time zone,
    etat integer DEFAULT 0 NOT NULL,
    parapheur_etat smallint,
    parapheur_commentaire character varying(1000),
    sae_etat boolean,
    reporte boolean DEFAULT false NOT NULL,
    montant integer,
    debat bytea,
    debat_name character varying(255),
    debat_size integer,
    debat_type character varying(255),
    avis integer,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    vote_nb_oui integer,
    vote_nb_non integer,
    vote_nb_abstention integer,
    vote_nb_retrait integer,
    vote_commentaire character varying(1000),
    delib_pdf bytea,
    bordereau bytea,
    signature bytea,
    signee boolean,
    commission bytea,
    commission_size integer,
    commission_type character varying(255),
    commission_name character varying(255),
    date_acte timestamp without time zone,
    date_envoi_signature timestamp without time zone,
    parapheur_id character varying(50),
    tdt_data_pdf bytea,
    tdt_data_bordereau_pdf bytea,
    president_id integer,
    parapheur_cible character varying,
    parapheur_bordereau bytea
);


--
-- Name: deliberations_seances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE deliberations_seances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: deliberations_seances; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE deliberations_seances (
    id integer DEFAULT nextval('deliberations_seances_id_seq'::regclass) NOT NULL,
    deliberation_id integer NOT NULL,
    seance_id integer NOT NULL,
    "position" integer,
    avis boolean,
    commentaire character varying(1000)
);


--
-- Name: deliberations_typeseances; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE deliberations_typeseances (
    id integer NOT NULL,
    deliberation_id integer NOT NULL,
    typeseance_id integer NOT NULL
);


--
-- Name: deliberations_typeseances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE deliberations_typeseances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: deliberations_typeseances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE deliberations_typeseances_id_seq OWNED BY deliberations_typeseances.id;


--
-- Name: historiques_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE historiques_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: historiques; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE historiques (
    id integer DEFAULT nextval('historiques_id_seq'::regclass) NOT NULL,
    delib_id integer NOT NULL,
    user_id integer NOT NULL,
    circuit_id integer NOT NULL,
    commentaire text NOT NULL,
    modified timestamp without time zone NOT NULL,
    created timestamp without time zone NOT NULL
);


--
-- Name: infosupdefs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE infosupdefs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: infosupdefs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE infosupdefs (
    id integer DEFAULT nextval('infosupdefs_id_seq'::regclass) NOT NULL,
    model character varying(25) DEFAULT 'Deliberation'::character varying NOT NULL,
    nom character varying(255) NOT NULL,
    commentaire character varying(255) NOT NULL,
    ordre integer NOT NULL,
    code character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    val_initiale character varying(1000),
    recherche boolean DEFAULT false NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    actif boolean
);


--
-- Name: infosupdefs_profils; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE infosupdefs_profils (
    id integer NOT NULL,
    profil_id integer NOT NULL,
    infosupdef_id integer NOT NULL
);


--
-- Name: infosupdefs_profils_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE infosupdefs_profils_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: infosupdefs_profils_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE infosupdefs_profils_id_seq OWNED BY infosupdefs_profils.id;


--
-- Name: infosuplistedefs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE infosuplistedefs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: infosuplistedefs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE infosuplistedefs (
    id integer DEFAULT nextval('infosuplistedefs_id_seq'::regclass) NOT NULL,
    infosupdef_id integer NOT NULL,
    ordre integer NOT NULL,
    nom character varying(255) NOT NULL,
    actif boolean DEFAULT true NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: infosups_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE infosups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: infosups; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE infosups (
    id integer DEFAULT nextval('infosups_id_seq'::regclass) NOT NULL,
    model character varying(25) DEFAULT 'Deliberation'::character varying NOT NULL,
    foreign_key integer NOT NULL,
    infosupdef_id integer,
    text character varying(1000),
    date date,
    file_name character varying(255),
    file_size integer,
    file_type character varying(255),
    content bytea
);


--
-- Name: listepresences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE listepresences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: listepresences; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE listepresences (
    id integer DEFAULT nextval('listepresences_id_seq'::regclass) NOT NULL,
    delib_id integer NOT NULL,
    acteur_id integer NOT NULL,
    present boolean NOT NULL,
    mandataire integer,
    suppleant_id integer
);


--
-- Name: models_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE models_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modelsections; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modelsections (
    id integer NOT NULL,
    name character varying(255),
    description character varying(255),
    parent_id integer DEFAULT 1,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: modelsections_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE modelsections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modelsections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE modelsections_id_seq OWNED BY modelsections.id;


--
-- Name: modeltemplates; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modeltemplates (
    id integer NOT NULL,
    name character varying(100),
    filename character varying(255),
    filesize integer,
    content bytea,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    modeltype_id integer DEFAULT 1
);

--
-- Name: modeltemplates_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE modeltemplates_id_seq
START WITH 1
INCREMENT BY 1
NO MINVALUE
NO MAXVALUE
CACHE 1;


--
-- Name: modeltemplates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE modeltemplates_id_seq OWNED BY modeltemplates.id;


--
-- Name: modeltypes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modeltypes (
    id integer NOT NULL,
    name character varying(255),
    description character varying(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: modeltypes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE modeltypes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modeltypes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE modeltypes_id_seq OWNED BY modeltypes.id;


--
-- Name: modelvalidations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modelvalidations (
    id integer NOT NULL,
    modelvariable_id integer,
    modelsection_id integer NOT NULL,
    modeltype_id integer NOT NULL,
    min integer DEFAULT 0,
    max integer,
    actif boolean DEFAULT true
);


--
-- Name: modelvalidations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE modelvalidations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modelvalidations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE modelvalidations_id_seq OWNED BY modelvalidations.id;


--
-- Name: modelvariables; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modelvariables (
    id integer NOT NULL,
    name character varying(255),
    description character varying(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: modelvariables_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE modelvariables_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modelvariables_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE modelvariables_id_seq OWNED BY modelvariables.id;


--
-- Name: natures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE natures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: natures; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE natures (
    id integer DEFAULT nextval('natures_id_seq'::regclass) NOT NULL,
    libelle character varying(100) NOT NULL,
    code character varying(3) NOT NULL,
    dua character varying(50),
    sortfinal character varying(50),
    communicabilite character varying(50)
);


--
-- Name: nomenclatures; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE nomenclatures (
    id character varying NOT NULL,
    parent_id character varying DEFAULT 0 NOT NULL,
    libelle character varying NOT NULL,
    lft integer DEFAULT 0,
    rght integer DEFAULT 0,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: nomenclatures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nomenclatures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profils_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE profils_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profils; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE profils (
    id integer DEFAULT nextval('profils_id_seq'::regclass) NOT NULL,
    parent_id integer DEFAULT 0,
    libelle character varying(100) DEFAULT ''::character varying NOT NULL,
    actif boolean DEFAULT true NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: seances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE seances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: seances; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE seances (
    id integer DEFAULT nextval('seances_id_seq'::regclass) NOT NULL,
    type_id integer DEFAULT 0 NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    date_convocation timestamp without time zone,
    date timestamp without time zone NOT NULL,
    traitee integer DEFAULT 0 NOT NULL,
    commentaire character varying(500),
    secretaire_id integer,
    president_id integer,
    debat_global bytea,
    debat_global_name character varying(75),
    debat_global_size integer,
    debat_global_type character varying(255),
    pv_figes smallint,
    pv_sommaire bytea,
    pv_complet bytea,
    numero_depot integer DEFAULT 0 NOT NULL
);


--
-- Name: sequences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE sequences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: sequences; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sequences (
    id integer DEFAULT nextval('sequences_id_seq'::regclass) NOT NULL,
    nom character varying(255) NOT NULL,
    commentaire character varying(255) NOT NULL,
    num_sequence integer NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: services; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE services (
    id integer DEFAULT nextval('services_id_seq'::regclass) NOT NULL,
    parent_id integer DEFAULT 0,
    "order" character varying(50) NOT NULL,
    libelle character varying(100) NOT NULL,
    circuit_defaut_id integer NOT NULL,
    actif boolean DEFAULT true NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    lft integer DEFAULT 0,
    rght integer DEFAULT 0
);


--
-- Name: tdt_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE tdt_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tdt_messages; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE tdt_messages (
    id integer DEFAULT nextval('tdt_messages_id_seq'::regclass) NOT NULL,
    delib_id integer NOT NULL,
    message_id integer NOT NULL,
    type_message integer NOT NULL,
    type_reponse integer,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    date_message date,
    data bytea
);


--
-- Name: themes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE themes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: themes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE themes (
    id integer DEFAULT nextval('themes_id_seq'::regclass) NOT NULL,
    parent_id integer DEFAULT 0,
    "order" character varying(50) NOT NULL,
    libelle character varying(500),
    actif boolean DEFAULT true NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    lft integer DEFAULT 0,
    rght integer DEFAULT 0
);


--
-- Name: traitements_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE traitements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: traitements; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE traitements (
    id integer DEFAULT nextval('traitements_id_seq'::regclass) NOT NULL,
    delib_id integer DEFAULT 0 NOT NULL,
    circuit_id integer DEFAULT 0 NOT NULL,
    "position" integer DEFAULT 0 NOT NULL,
    date_traitement timestamp without time zone
);


--
-- Name: typeactes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE typeactes (
    id integer NOT NULL,
    libelle text NOT NULL,
    modeleprojet_id integer NOT NULL,
    modelefinal_id integer NOT NULL,
    nature_id integer NOT NULL,
    compteur_id integer NOT NULL,
    created date NOT NULL,
    modified date NOT NULL,
    gabarit_projet bytea,
    gabarit_synthese bytea,
    gabarit_acte bytea,
    teletransmettre boolean DEFAULT true
);


--
-- Name: typeactes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE typeactes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: typeactes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE typeactes_id_seq OWNED BY typeactes.id;


--
-- Name: typeacteurs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE typeacteurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: typeacteurs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE typeacteurs (
    id integer DEFAULT nextval('typeacteurs_id_seq'::regclass) NOT NULL,
    nom character varying(255) NOT NULL,
    commentaire character varying(255) NOT NULL,
    elu boolean NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: typeseances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE typeseances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: typeseances; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE typeseances (
    id integer DEFAULT nextval('typeseances_id_seq'::regclass) NOT NULL,
    libelle character varying(100) NOT NULL,
    retard integer DEFAULT 0,
    action smallint NOT NULL,
    compteur_id integer NOT NULL,
    modelprojet_id integer NOT NULL,
    modeldeliberation_id integer NOT NULL,
    modelconvocation_id integer NOT NULL,
    modelordredujour_id integer NOT NULL,
    modelpvsommaire_id integer NOT NULL,
    modelpvdetaille_id integer NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: typeseances_acteurs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE typeseances_acteurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: typeseances_acteurs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE typeseances_acteurs (
    id integer DEFAULT nextval('typeseances_acteurs_id_seq'::regclass) NOT NULL,
    typeseance_id integer NOT NULL,
    acteur_id integer NOT NULL
);


--
-- Name: typeseances_natures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE typeseances_natures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: typeseances_typeactes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE typeseances_typeactes (
    id integer DEFAULT nextval('typeseances_natures_id_seq'::regclass) NOT NULL,
    typeseance_id integer NOT NULL,
    typeacte_id integer NOT NULL
);


--
-- Name: typeseances_typeacteurs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE typeseances_typeacteurs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: typeseances_typeacteurs; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE typeseances_typeacteurs (
    id integer DEFAULT nextval('typeseances_typeacteurs_id_seq'::regclass) NOT NULL,
    typeseance_id integer NOT NULL,
    typeacteur_id integer NOT NULL
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users (
    id integer DEFAULT nextval('users_id_seq'::regclass) NOT NULL,
    profil_id integer DEFAULT 0 NOT NULL,
    statut integer DEFAULT 0 NOT NULL,
    login character varying(50) DEFAULT ''::character varying NOT NULL,
    note character varying(300),
    circuit_defaut_id integer,
    password character varying(100) DEFAULT ''::character varying NOT NULL,
    nom character varying(50) DEFAULT ''::character varying NOT NULL,
    prenom character varying(50) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL,
    telfixe character varying(20),
    telmobile character varying(20),
    date_naissance date,
    accept_notif boolean,
    mail_refus boolean NOT NULL,
    mail_traitement boolean NOT NULL,
    mail_insertion boolean NOT NULL,
    "position" integer,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    mail_modif_projet_cree boolean DEFAULT false,
    mail_modif_projet_valide boolean DEFAULT false,
    mail_retard_validation boolean DEFAULT false
);


--
-- Name: users_services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_services; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users_services (
    id integer DEFAULT nextval('users_services_id_seq'::regclass) NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    service_id integer DEFAULT 0 NOT NULL
);


--
-- Name: votes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE votes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: votes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE votes (
    id integer DEFAULT nextval('votes_id_seq'::regclass) NOT NULL,
    acteur_id integer DEFAULT 0 NOT NULL,
    delib_id integer DEFAULT 0 NOT NULL,
    resultat integer,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


--
-- Name: wkf_circuits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE wkf_circuits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wkf_circuits; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE wkf_circuits (
    id integer DEFAULT nextval('wkf_circuits_id_seq'::regclass) NOT NULL,
    nom character varying(250) NOT NULL,
    description text,
    actif boolean DEFAULT true NOT NULL,
    defaut boolean DEFAULT false NOT NULL,
    created_user_id integer,
    modified_user_id integer,
    created timestamp without time zone,
    modified timestamp without time zone
);


--
-- Name: wkf_compositions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE wkf_compositions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wkf_compositions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE wkf_compositions (
    id integer DEFAULT nextval('wkf_compositions_id_seq'::regclass) NOT NULL,
    etape_id integer NOT NULL,
    type_validation character varying(1) NOT NULL,
    soustype integer,
    type_composition character varying(20) DEFAULT 'USER'::character varying,
    trigger_id integer,
    created_user_id integer,
    modified_user_id integer,
    created timestamp without time zone,
    modified timestamp without time zone
);


--
-- Name: wkf_etapes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE wkf_etapes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wkf_etapes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE wkf_etapes (
    id integer DEFAULT nextval('wkf_etapes_id_seq'::regclass) NOT NULL,
    circuit_id integer NOT NULL,
    nom character varying(250) NOT NULL,
    description text,
    type integer NOT NULL,
    soustype integer,
    ordre integer NOT NULL,
    created_user_id integer NOT NULL,
    modified_user_id integer,
    created timestamp without time zone,
    modified timestamp without time zone,
    cpt_retard integer
);


--
-- Name: wkf_signatures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE wkf_signatures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wkf_signatures; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE wkf_signatures (
    id integer DEFAULT nextval('wkf_signatures_id_seq'::regclass) NOT NULL,
    type_signature character varying(100) NOT NULL,
    signature text NOT NULL,
    visa_id integer
);


--
-- Name: wkf_traitements_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE wkf_traitements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wkf_traitements; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE wkf_traitements (
    id integer DEFAULT nextval('wkf_traitements_id_seq'::regclass) NOT NULL,
    circuit_id integer NOT NULL,
    target_id integer NOT NULL,
    numero_traitement integer DEFAULT 1 NOT NULL,
    treated_orig smallint DEFAULT 0 NOT NULL,
    created_user_id integer,
    modified_user_id integer,
    created timestamp without time zone,
    modified timestamp without time zone,
    treated boolean
);


--
-- Name: wkf_visas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE wkf_visas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: wkf_visas; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE wkf_visas (
    id integer DEFAULT nextval('wkf_visas_id_seq'::regclass) NOT NULL,
    traitement_id integer NOT NULL,
    trigger_id integer NOT NULL,
    signature_id integer,
    etape_nom character varying(250),
    etape_type integer NOT NULL,
    action character varying(2) NOT NULL,
    commentaire text,
    date timestamp without time zone,
    numero_traitement integer NOT NULL,
    type_validation character varying(1) NOT NULL,
    etape_id integer,
    date_retard timestamp without time zone
);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY acteurs_seances ALTER COLUMN id SET DEFAULT nextval('acteurs_seances_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY crons ALTER COLUMN id SET DEFAULT nextval('crons_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY deliberations_typeseances ALTER COLUMN id SET DEFAULT nextval('deliberations_typeseances_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY infosupdefs_profils ALTER COLUMN id SET DEFAULT nextval('infosupdefs_profils_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelsections ALTER COLUMN id SET DEFAULT nextval('modelsections_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY modeltemplates ALTER COLUMN id SET DEFAULT nextval('modeltemplates_id_seq'::regclass);

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY modeltypes ALTER COLUMN id SET DEFAULT nextval('modeltypes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelvalidations ALTER COLUMN id SET DEFAULT nextval('modelvalidations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelvariables ALTER COLUMN id SET DEFAULT nextval('modelvariables_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY typeactes ALTER COLUMN id SET DEFAULT nextval('typeactes_id_seq'::regclass);


--
-- Data for Name: acos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY acos (id, alias, lft, rght, parent_id, model, foreign_key) FROM stdin;
1	Pages:home	1	2	0	\N	0
3	Deliberations:add	4	5	2	\N	0
4	Deliberations:mesProjetsRedaction	6	7	2	\N	0
5	Deliberations:mesProjetsValidation	8	9	2	\N	0
6	Deliberations:mesProjetsATraiter	10	11	2	\N	0
7	Deliberations:mesProjetsValides	12	13	2	\N	0
2	Pages:mes_projets	3	16	0	\N	0
8	Deliberations:mesProjetsRecherche	14	15	2	\N	0
9	Pages:projets_mon_service	17	20	0	\N	0
10	Deliberations:projetsMonService	18	19	9	\N	0
12	Deliberations:tousLesProjetsSansSeance	22	23	11	\N	0
13	Deliberations:tousLesProjetsValidation	24	25	11	\N	0
14	Deliberations:tousLesProjetsAFaireVoter	26	27	11	\N	0
11	Pages:tous_les_projets	21	30	0	\N	0
15	Deliberations:tousLesProjetsRecherche	28	29	11	\N	0
17	Deliberations:autresActesAValider	32	33	16	\N	0
18	Deliberations:autreActesValides	34	35	16	\N	0
19	Deliberations:autreActesAEnvoyer	36	37	16	\N	0
20	Deliberations:autreActesEnvoyes	38	39	16	\N	0
16	Pages:autresActesAValider	31	42	0	\N	0
22	Seances:add	44	45	21	\N	0
23	Seances:listerFuturesSeances	46	47	21	\N	0
24	Seances:listerAnciennesSeances	48	49	21	\N	0
21	Pages:listerFuturesSeances	43	52	0	\N	0
25	Seances:afficherCalendrier	50	51	21	\N	0
27	Postseances:index	54	55	26	\N	0
28	Deliberations:sendToParapheur	56	57	26	\N	0
29	Deliberations:toSend	58	59	26	\N	0
30	Deliberations:transmit	60	61	26	\N	0
26	Pages:postseances	53	64	0	\N	0
31	Deliberations:verserAsalae	62	63	26	\N	0
33	Profils:index	66	67	32	\N	0
34	Services:index	68	69	32	\N	0
35	Users:index	70	71	32	\N	0
32	Pages:gestion_utilisateurs	65	74	0	\N	0
38	Typeacteurs:index	76	77	37	\N	0
37	Pages:gestion_acteurs	75	80	0	\N	0
39	Acteurs:index	78	79	37	\N	0
41	Collectivites:index	82	83	40	\N	0
42	Themes:index	84	85	40	\N	0
44	Sequences:index	86	87	40	\N	0
45	Compteurs:index	88	89	40	\N	0
46	Typeactes:index	90	91	40	\N	0
47	Typeseances:index	92	93	40	\N	0
48	Infosupdefs:index	94	95	40	\N	0
49	Infosupdefs:index_seance	96	97	40	\N	0
40	Pages:administration	81	100	0	\N	0
51	Deliberations:edit	102	103	50	\N	0
62	Deliberations:nonTransmis	40	41	16	\N	0
63	Circuits:index	72	73	32	\N	0
52	Deliberations:delete	104	105	50	\N	0
54	Deliberations:goNext	106	107	50	\N	0
55	Deliberations:validerEnUrgence	108	109	50	\N	0
56	Deliberations:rebond	110	111	50	\N	0
57	Deliberations:sendToGed	112	113	50	\N	0
64	Model_odt_validator:modeltemplates	98	99	40	\N	0
50	Module:Deliberations	101	116	0	\N	0
66	Connecteurs:index	118	119	65	\N	0
65	Pages:connecteurs	117	122	0	\N	0
67	Crons:index	120	121	65	\N	0
69	Crons:add	124	125	68	\N	0
70	Crons:edit	126	127	68	\N	0
68	Module:Crons	123	130	0	\N	0
71	Crons:delete	128	129	68	\N	0
72	Deliberations:editerTous	114	115	50	\N	0
\.


--
-- Name: acos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acos_id_seq', 72, true);


--
-- Data for Name: acteurs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY acteurs (id, typeacteur_id, nom, prenom, salutation, titre, "position", date_naissance, adresse1, adresse2, cp, ville, email, telfixe, telmobile, suppleant_id, note, actif, created, modified) FROM stdin;
\.


--
-- Name: acteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acteurs_id_seq', 1, false);


--
-- Data for Name: acteurs_seances; Type: TABLE DATA; Schema: public; Owner: -
--

COPY acteurs_seances (id, acteur_id, seance_id, mail_id, date_envoi, date_reception, model) FROM stdin;
\.


--
-- Name: acteurs_seances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acteurs_seances_id_seq', 1, false);


--
-- Data for Name: acteurs_services; Type: TABLE DATA; Schema: public; Owner: -
--

COPY acteurs_services (id, acteur_id, service_id) FROM stdin;
\.


--
-- Name: acteurs_services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acteurs_services_id_seq', 1, false);


--
-- Data for Name: ados; Type: TABLE DATA; Schema: public; Owner: -
--

COPY ados (id, alias, lft, rght, parent_id, model, foreign_key) FROM stdin;
1	Typeacte:Délibération	1	2	0	Typeacte	2
\.


--
-- Name: ados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ados_id_seq', 1, true);


--
-- Data for Name: annexes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY annexes (id, model, foreign_key, joindre_ctrl_legalite, joindre_fusion, titre, filename, filetype, size, data, filename_pdf, data_pdf, created, modified, edition_data, edition_data_typemime) FROM stdin;
\.


--
-- Name: annexes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('annexes_id_seq', 1, false);


--
-- Data for Name: aros; Type: TABLE DATA; Schema: public; Owner: -
--

COPY aros (id, foreign_key, alias, lft, rght, parent_id, model) FROM stdin;
1	1	Administrateur	1	4	0	Profil
3	1	admin	2	3	1	User
\.


--
-- Data for Name: aros_acos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) FROM stdin;
1	3	1	1 	1 	1 	1 
2	3	2	1 	1 	1 	1 
3	3	3	1 	1 	1 	1 
4	3	4	1 	1 	1 	1 
5	3	5	1 	1 	1 	1 
6	3	6	1 	1 	1 	1 
7	3	7	1 	1 	1 	1 
8	3	8	1 	1 	1 	1 
9	3	9	1 	1 	1 	1 
10	3	10	1 	1 	1 	1 
11	3	11	1 	1 	1 	1 
12	3	12	1 	1 	1 	1 
13	3	13	1 	1 	1 	1 
14	3	14	1 	1 	1 	1 
15	3	15	1 	1 	1 	1 
16	3	16	1 	1 	1 	1 
17	3	17	1 	1 	1 	1 
18	3	18	1 	1 	1 	1 
19	3	19	1 	1 	1 	1 
20	3	20	1 	1 	1 	1 
62	3	62	1 	1 	1 	1 
21	3	21	1 	1 	1 	1 
22	3	22	1 	1 	1 	1 
23	3	23	1 	1 	1 	1 
24	3	24	1 	1 	1 	1 
25	3	25	1 	1 	1 	1 
26	3	26	1 	1 	1 	1 
27	3	27	1 	1 	1 	1 
28	3	28	1 	1 	1 	1 
29	3	29	1 	1 	1 	1 
30	3	30	1 	1 	1 	1 
31	3	31	1 	1 	1 	1 
32	3	32	1 	1 	1 	1 
33	3	33	1 	1 	1 	1 
34	3	34	1 	1 	1 	1 
35	3	35	1 	1 	1 	1 
63	3	63	1 	1 	1 	1 
37	3	37	1 	1 	1 	1 
38	3	38	1 	1 	1 	1 
39	3	39	1 	1 	1 	1 
40	3	40	1 	1 	1 	1 
41	3	41	1 	1 	1 	1 
42	3	42	1 	1 	1 	1 
64	3	64	1 	1 	1 	1 
44	3	44	1 	1 	1 	1 
45	3	45	1 	1 	1 	1 
48	3	48	1 	1 	1 	1 
49	3	49	1 	1 	1 	1 
46	3	46	1 	1 	1 	1 
47	3	47	1 	1 	1 	1 
65	3	65	1 	1 	1 	1 
66	3	66	1 	1 	1 	1 
67	3	67	1 	1 	1 	1 
68	3	68	1 	1 	1 	1 
69	3	69	1 	1 	1 	1 
70	3	70	1 	1 	1 	1 
71	3	71	1 	1 	1 	1 
50	3	50	1 	1 	1 	1 
51	3	51	1 	1 	1 	1 
52	3	52	1 	1 	1 	1 
54	3	54	1 	1 	1 	1 
55	3	55	1 	1 	1 	1 
56	3	56	1 	1 	1 	1 
57	3	57	1 	1 	1 	1 
72	3	72	1 	1 	1 	1 
\.


--
-- Name: aros_acos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('aros_acos_id_seq', 72, true);


--
-- Data for Name: aros_ados; Type: TABLE DATA; Schema: public; Owner: -
--

COPY aros_ados (id, aro_id, ado_id, _create, _read, _update, _delete) FROM stdin;
1	3	1	1 	1 	1 	1 
\.


--
-- Name: aros_ados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('aros_ados_id_seq', 1, true);


--
-- Name: aros_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('aros_id_seq', 3, true);


--
-- Data for Name: circuits_users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY circuits_users (id, circuit_id, user_id) FROM stdin;
\.


--
-- Name: circuits_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('circuits_users_id_seq', 1, false);


--
-- Data for Name: collectivites; Type: TABLE DATA; Schema: public; Owner: -
--

COPY collectivites (id, id_entity, nom, adresse, "CP", ville, telephone, logo) FROM stdin;
1	1	ADULLACT	836, rue du Mas de Verchant	34000	Montpellier	04 67 65 05 88	\N
\.


--
-- Data for Name: commentaires; Type: TABLE DATA; Schema: public; Owner: -
--

COPY commentaires (id, delib_id, agent_id, texte, pris_en_compte, commentaire_auto, created, modified) FROM stdin;
\.


--
-- Name: commentaires_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('commentaires_id_seq', 1, false);


--
-- Data for Name: compteurs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY compteurs (id, nom, commentaire, def_compteur, sequence_id, def_reinit, val_reinit, created, modified) FROM stdin;
\.


--
-- Name: compteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('compteurs_id_seq', 1, true);


--
-- Data for Name: crons; Type: TABLE DATA; Schema: public; Owner: -
--

COPY crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) FROM stdin;
1	Circuits de traitement : Mise à jour des traitements extérieurs	Lecture de l'état des traitements extérieurs (iParapheur)	\N	delegationJob	f		2013-03-14 17:45:00	PT1H	2013-03-14 17:10:03	2013-03-14 17:10:03		SUCCES	t	2013-03-06 11:01:46.996708	1	2013-03-14 11:44:45	2	CronJob	f
2	Circuits de traitement : Déclenchement des alertes de retard	Relance les utilisateurs devant valider un projet en retard	\N	retardCakeflowJob	f	\N	2014-02-12 16:30:33.979674	P1D	2014-02-12 16:30:33.979674	2014-02-12 16:30:33.979674	Cette tâche n'a encore jamais été exécutée.	\N	t	2014-02-12 16:30:33.979674	1	2014-02-12 16:30:33.979674	1	CronJob	f
3	Signature : Mise à jour de l'etat des dossiers envoyés au Parapheur	Met à jour l'état des projets envoyés au Parapheur pour signature et rapatrie les informations de ceux en fin de circuit.	\N	signatureJob	f	\N	2014-02-12 16:30:33.979674	P1D	2014-02-12 16:30:33.979674	2014-02-12 16:30:33.979674	Cette tâche n'a encore jamais été exécutée.	\N	t	2014-02-12 16:30:33.979674	1	2014-02-12 16:30:33.979674	1	CronJob	f
4	TDT : Mise à jours des mails sécurisés	Envoi/Réception des mails sécurisés	\N	mailSecJob	f	\N	2014-02-12 16:30:33.979674	PT5M	2014-02-12 16:30:33.979674	2014-02-12 16:30:33.979674	Cette tâche n'a encore jamais été exécutée.	\N	t	2014-02-12 16:30:33.979674	1	2014-02-12 16:30:33.979674	1	CronJob	f
5	TDT : Mise à jour des accusés de réception	Vérifie la réception par la prefecture des dossiers envoyés via le TDT et dans le cas échéant, enregistre la date de l'accusé de réception et le bordereau	\N	majArTdt	f	\N	2014-02-12 16:30:33.979674	PT5M	2014-02-12 16:30:33.979674	2014-02-12 16:30:33.979674	Cette tâche n'a encore jamais été exécutée.	\N	t	2014-02-12 16:30:33.979674	1	2014-02-12 16:30:33.979674	1	CronJob	f
6	TDT : Mise à jour des échanges de courriers	Met à jour les échanges de courriers entre la préfecture et le TDT	\N	majCourriersTdt	f	\N	2014-02-12 16:30:33.979674	P1D	2014-02-12 16:30:33.979674	2014-02-12 16:30:33.979674	Cette tâche n'a encore jamais été exécutée.	\N	t	2014-02-12 16:30:33.979674	1	2014-02-12 16:30:33.979674	1	CronJob	f
7	CONVERTION : convertion des annexes	convertion des annexes dans différents formats	\N	convertionAnnexesJob	f	\N	2014-02-12 16:30:33.979674	P1D	2014-02-12 16:30:33.979674	2014-02-12 16:30:33.979674	Cette tâche n'a encore jamais été exécutée.	\N	t	2014-02-12 16:30:33.979674	1	2014-02-12 16:30:33.979674	1	CronJob	f
\.


--
-- Name: crons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('crons_id_seq', 7, true);


--
-- Data for Name: deliberations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY deliberations (id, typeacte_id, circuit_id, theme_id, service_id, redacteur_id, rapporteur_id, anterieure_id, is_multidelib, parent_id, objet, objet_delib, titre, num_delib, num_pref, pastell_id, tdt_id, "tdt_dateAR", texte_projet, texte_projet_name, texte_projet_type, texte_projet_size, texte_synthese, texte_synthese_name, texte_synthese_type, texte_synthese_size, deliberation, deliberation_name, deliberation_type, deliberation_size, date_limite, date_envoi, etat, parapheur_etat, parapheur_commentaire, sae_etat, reporte, montant, debat, debat_name, debat_size, debat_type, avis, created, modified, vote_nb_oui, vote_nb_non, vote_nb_abstention, vote_nb_retrait, vote_commentaire, delib_pdf, bordereau, signature, signee, commission, commission_size, commission_type, commission_name, date_acte, date_envoi_signature, parapheur_id, tdt_data_pdf, tdt_data_bordereau_pdf, president_id, parapheur_cible, parapheur_bordereau) FROM stdin;
\.


--
-- Name: deliberations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('deliberations_id_seq', 1, true);


--
-- Data for Name: deliberations_seances; Type: TABLE DATA; Schema: public; Owner: -
--

COPY deliberations_seances (id, deliberation_id, seance_id, "position", avis, commentaire) FROM stdin;
\.


--
-- Name: deliberations_seances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('deliberations_seances_id_seq', 1, false);


--
-- Data for Name: deliberations_typeseances; Type: TABLE DATA; Schema: public; Owner: -
--

COPY deliberations_typeseances (id, deliberation_id, typeseance_id) FROM stdin;
\.


--
-- Name: deliberations_typeseances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('deliberations_typeseances_id_seq', 1, false);


--
-- Data for Name: historiques; Type: TABLE DATA; Schema: public; Owner: -
--

COPY historiques (id, delib_id, user_id, circuit_id, commentaire, modified, created) FROM stdin;
\.


--
-- Name: historiques_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('historiques_id_seq', 1, false);


--
-- Data for Name: infosupdefs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY infosupdefs (id, model, nom, commentaire, ordre, code, type, val_initiale, recherche, created, modified, actif) FROM stdin;
\.


--
-- Name: infosupdefs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosupdefs_id_seq', 1, false);


--
-- Data for Name: infosupdefs_profils; Type: TABLE DATA; Schema: public; Owner: -
--

COPY infosupdefs_profils (id, profil_id, infosupdef_id) FROM stdin;
\.


--
-- Name: infosupdefs_profils_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosupdefs_profils_id_seq', 1, false);


--
-- Data for Name: infosuplistedefs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY infosuplistedefs (id, infosupdef_id, ordre, nom, actif, created, modified) FROM stdin;
\.


--
-- Name: infosuplistedefs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosuplistedefs_id_seq', 1, false);


--
-- Data for Name: infosups; Type: TABLE DATA; Schema: public; Owner: -
--

COPY infosups (id, model, foreign_key, infosupdef_id, text, date, file_name, file_size, file_type, content) FROM stdin;
\.


--
-- Name: infosups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosups_id_seq', 1, false);


--
-- Data for Name: listepresences; Type: TABLE DATA; Schema: public; Owner: -
--

COPY listepresences (id, delib_id, acteur_id, present, mandataire, suppleant_id) FROM stdin;
\.


--
-- Name: listepresences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('listepresences_id_seq', 1, false);


--
-- Data for Name: modelsections; Type: TABLE DATA; Schema: public; Owner: -
--

COPY modelsections (id, name, description, parent_id, created, modified) FROM stdin;
1	#Document	En dehors de toutes sections	1	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
2	Projets	Itération sur les projets	1	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
3	Seances	Itération sur les seances	1	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
4	AvisProjet	Itération sur les AvisProjet	1	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
5	Annexes	Itération sur les annexes	1	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
\.


--
-- Name: modelsections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modelsections_id_seq', 5, false);


--
-- Data for Name: modeltemplates; Type: TABLE DATA; Schema: public; Owner: -
--

COPY modeltemplates (id, name, filename, filesize, content, created, modified, modeltype_id) FROM stdin;
1	Défaut	modeledefaut.odt	11805	\\x504b0304140000080000876151415ec6320c2700000027000000080000006d696d65747970656170706c69636174696f6e2f766e642e6f617369732e6f70656e646f63756d656e742e74657874504b030414000008000087615141781176c4e5030000e5030000080000006d6574612e786d6c3c3f786d6c2076657273696f6e3d22312e302220656e636f64696e673d225554462d38223f3e0a3c6f66666963653a646f63756d656e742d6d65746120786d6c6e733a6f66666963653d2275726e3a6f617369733a6e616d65733a74633a6f70656e646f63756d656e743a786d6c6e733a6f66666963653a312e302220786d6c6e733a786c696e6b3d22687474703a2f2f7777772e77332e6f72672f313939392f786c696e6b2220786d6c6e733a64633d22687474703a2f2f7075726c2e6f72672f64632f656c656d656e74732f312e312f2220786d6c6e733a6d6574613d2275726e3a6f617369733a6e616d65733a74633a6f70656e646f63756d656e743a786d6c6e733a6d6574613a312e302220786d6c6e733a6f6f6f3d22687474703a2f2f6f70656e6f66666963652e6f72672f323030342f6f66666963652220786d6c6e733a677264646c3d22687474703a2f2f7777772e77332e6f72672f323030332f672f646174612d766965772322206f66666963653a76657273696f6e3d22312e32223e3c6f66666963653a6d6574613e3c6d6574613a6372656174696f6e2d646174653e323030392d31312d31395431313a34313a30392e30373c2f6d6574613a6372656174696f6e2d646174653e3c64633a646174653e323031322d31302d31375431343a31323a31353c2f64633a646174653e3c6d6574613a65646974696e672d6475726174696f6e3e5054324833304d3537533c2f6d6574613a65646974696e672d6475726174696f6e3e3c6d6574613a65646974696e672d6379636c65733e33303c2f6d6574613a65646974696e672d6379636c65733e3c6d6574613a67656e657261746f723e4c696272654f66666963652f332e35244c696e75785f5838365f3634204c696272654f66666963655f70726f6a6563742f3335306d31244275696c642d323c2f6d6574613a67656e657261746f723e3c64633a63726561746f723e6672616e636f6973203c2f64633a63726561746f723e3c6d6574613a646f63756d656e742d737461746973746963206d6574613a7461626c652d636f756e743d223022206d6574613a696d6167652d636f756e743d223022206d6574613a6f626a6563742d636f756e743d223022206d6574613a706167652d636f756e743d223222206d6574613a7061726167726170682d636f756e743d22313022206d6574613a776f72642d636f756e743d22343122206d6574613a6368617261637465722d636f756e743d2232313822206d6574613a6e6f6e2d776869746573706163652d6368617261637465722d636f756e743d22313833222f3e3c2f6f66666963653a6d6574613e3c2f6f66666963653a646f63756d656e742d6d6574613e504b0304140008080800876151410000000000000000000000000c00000073657474696e67732e786d6cbd5a4d73e23810bdefaf48f94e0899149b500953860c334c48a0804c6ae726ec06b491d52e490ef0efa765039b059c658cb5270a7f744bad7efd5e37dc7e5e46e2ec0d94e628efbcdaf985770632c090cbd99df73cee54aebdcfcd3f6e713ae50134420c9208a4a96830861ed167f4bad48decf69d9728d940a6b96e4816816e98a08131c8cd6b8df74f375267d995a5e0f2f5ce9b1b1337aad5c56271bef8748e6a56addddcdc54d3bb9b470394533e3bd655f6f47b5788b875645fc816933abbbcb8b8aa66dfbdb3f522df85e6d26b6ee2b0d97ef376ed20fba87003918dcdd9fab25dda9d472e1b6f1c16dba87987defbf73b3fe8795f011b63ec6dee98554c7704ca99d7bcb8adee9b38de6c0fa6c685dd171e9af941c357b55aedfa34e3df80cfe687975dabd78b5a1fcd71318490720cda732667a0773c4c100530e9358d4aa0988fae6c295c6878c410f2ac4f99d0479baf442cae7019c212c2fd601d4eb0f41d82865a1d17f26eb8b3546d14b7c1b6b97c59fc287373af7679513f21fff2a0f2e775fd5351ab9a4f04940f96d46ce9d04ead0e73316211583fc9760b8dc1281780058dff448cc6646a37dbe6a8cc4905a9c7569898368a2492bba02ecb7a0bf1b53454efc7a5c30283eaf0da6b170557dfd523101018083b8a2e1458fa818befab4bdeed75c13afc0091e3f1749a5d48143344cebfc3ab032a6166cc28991d94793f0c074cb1d4fc286601adc93723c3d42e204bf0455ca58c9f187c8e4366720ff104ced23d360171bf16540e12fc9199798b69b0d4eb0b3e938efcf413635df4e00dc45f1c44a89f926802299995ef2dcd304b1903e6444ba491a2141b198c07a8b94540f95eeef109cdf7441b3e5df52878fa859bf9239309132d9262afae023786a5190816c01c4508bb95af0c37cf1afa22749e0103853aa6223b1cf79cb84043c63ba876e9b80cebed442982e23d336c42f86c63143199a306ab47c6a34587fada4169722171c282471113a2cd623d0015d0ca0979f5ba9b9a4b840c8a22122bd0b6332c5db9a4d16a53a0140a07e52335ff8d8721488b350787d1d50fa0a4af399383440626611f95a8d3906c0100ca6ee445b138fe1ff0eccafe9728362b4794f12c27b6665b59b2ad7b2e60b83d90fee46f0ad5869d1c9eca10ec8006766b5319f68962f5100425ef1bb56a5d92aef9e2a4f8d1a45a71888b0780dd6eb0944a8e52f3f01f84f4251d0d9d8bb3f3b0dda7a32cce60c862501d85d1084ce22262a4e1d3aea83f9d525be10425a960b77b1843140b27e27ddd89ac9b9031a669d606e18c5146e446c0779cb88818ede6cb92685732d103167e504f4edc05288b733b4626b0670d7ece0c6c4e895e21098022b125ae984f12d6af59361c18216cbafc13b6e34b892665df7cfb05554aa6a2d7417b048a4ae0e2e4d783d9b5dca58d3cc06ad70d29d4fa558b4ba65647ab51504ffb938fe3156d1b8520b50929695b5859b039cbc983a5ee3777dd9d4954d0e14a1bdbce6574d6952edba0ae246e362d54c43feb42e44c9c11e3b4a8bf98294cf61a95b2ba61ea2f4ca2e05eb10591a8ee4bebd58df0e853e4a602172e6a691c8b15a157d9eece899ef1d3e947966124072d3466243de6ba2b7b5cbb10d0cf72498e02f8090a8928fe83234ee933d91bfcc87e11eccbb640ede2fcd38ceeb0e549152a356253f523a95feaa4b37c273d64e190ce12a5d82bfa65c5f9abcdcc0fb8abf8ea3b76d2e87240eb0baa10e9264876b5990c4094efc466fc578113b61d045bd1e282ea77864ef67384890a8a6360db8e5ac673c74019b5da76d197614b300a0fb9b53edb4c04897035fe383ca53b308fa20c29acf352e61b02b54216efbe31ca6e8e64831de5b9d894e0b1afb783365f062453217c51f4a8ea8855da9b1549bfdc9f9caa7bffe9a8e6fddba5f90b504b0708963464fa6a0500002f230000504b0304140008080800876151410000000000000000000000000b000000636f6e74656e742e786d6ced5bdb6edb38107ddfaf10b4c03e955164a769ecad5d04bb9b4581a42dda14d8b78096689b2d256a49fad63fcafe467e6c87d4c5926d29722cbb4ed19714e2ccf00ccfcc7048c97dfd661e306b4a84a43cecd9eec9a96d91d0e33e0d473dfbf3ed15bab0dff47f79cd8743ea91aecfbd494042853c1e2af8d702eb50766369cf9e88b0cbb1a4b21be280c8aef2ba3c22616ad5cd6b770d563c22d582d53637ca796b45e6aaaeb1d62dd8e2417d64a39cb7f6059ed535d6ba406ade7cc8eb1acf2543430eac07115674c58b39a3e1d79e3d562aea3ace6c363b99b54fb818396ea7d3718c3473d8cbf4a2896046cbf71cc28806938e7be23aa96e4014aeeb9fd6cdbb144e820111b5a9c10aaf45554e47b533623a2aa1c61b63513b378c7231bc6dbf7e78db7ede36c06a5c12930be70684e6cfcdf532174450174beb16a8f2048d6a2f33d6cedb73ce3357b5415ca0c6ddd6e9e999133fe7b46795ea334115113975af52ddc3cccb18e7c126d240cf75400391a94ed32cf13511b2c4a0e5c4e24c59faa553ff7373fdc91b93002f95e9e3ca888652e170c98cd041285de94b4790880b951133acbf6142b45a996f6315b0f272d7d25475247c7fa32ab8d376a0f4a1f0d09492d9af85fdb03a1f3a8e51ca129712965649a69b2c87cc2322a85e09663a1150208134480e1e7573d6c53d5104f37ad3e984e0fe7075c695e2f0a46cab4d1cdc7e74b40ce996009b5e82946b852dbb9ff6bdb866a4930d0ca1ffa121f608f289c764ff75bc7f65c356fcacfdeed9b778cc03ecda166c54a94a40d92295d8ce23f6b06669bd2333eb2368871be6f90d475cfebea2170fda56616aad8f46240422a12c4532df5223a2ca838d6b8a0535517ec4b54b50631b1c4ac7cba1e58c4ab933f42652d7b1b1cf45683a5ccf7e07f97110d7accf21858314b16e3e95466c55b146c8e4422a12ece25d9274a5d9b83bba535625c9389e280052d443669eac7cccdf82af1fdc0c2b7132c2028f048ec6a90006f489d43ca0d8ea8a73dd81d269331314c13e4184a25026436e0e8308333a8272f748689a566cf16522151d2e9084131bcc3ae302b6b821663247aeb1ce4d98a3a4501b00640625fd0683ee69a40af4e961042d008765427db461649e8835bd39b62aa86b3d85ba4fd0d17c2cfcadc84be83a007b6e157b1b854bf6dcadd86b1f217b19797800b03c92ab03a9675c5265ba58e7a47376e16505ab1611c9727dc9456e3ea7dcef6713bab3230c9db6fc4a4884141f1135d69723cc667821d783eafc185178f9bca3f0b3b43605f5fc0883faa316d0ab83710dd758a17ef69fa6027771b0c0d53eb51e67e4d2d528814399bc018b40e861bd576f73a04ec766848ec670dd1f70e61fe898ddf919ed0346fb6cfb689f5547fb6cab68bba747d801b5e54010fc150d08104bb453a3e774d9729f74cdcf584d54022c215f915e7ba271f06364e6ac7621f908a2df761c69491e5f22ecf6d2a23411aea890eaae757af74117c65e36dd6716fa4676e3f3ed77e3f3ea7439df2e5d8ef12dcdb3d9766fd7765dedb1fda48c5903754adff5268201f717d9839ed8321fe7279220c9872aae5ed3d524382626ba6c8d8624ff4e48b87ca9bc3e18cfe4531931bc407ca2180d0962644a58cf86066ec431076f1983480aac0b4f2f62a7c96ed317e0bbcda283b0eb247fc63f37307129670dc816c87c83ab1acebe8d61362128de8d868c63651724ab2ef858913b49cc1752a7fecc100ded782a8a1f51821089877b197f03ce239101564d4341510554ea8f81052c68f701b983b2f842546358d02f1eee052f00a5eda0692c460784b187fb02181f0044d3487a1252244f8f340de33fdc0f742eae25844ff45a576a7be7b4a053caa85ae14f825019a03bec293211b048a213b5b914e14101119ef705f5c22a205d1a14f92146915067ba5f2a2e1a037c84ca40b76b85a968aeb8ad322ef780b599ce1b0344be039d78d068b69452d930ce661a2f07fb4aca295fd9bc7443d0bf3d81dcb833c203c54bffd6f210a9df30cee678fd6140be4bd2eb1fac36d909aa12bf69acd2e44f80f640a8e9d516345768a21b9af85e9a6bb1de708497e7d792a36a145be4af77fa25825321eed8fdebf88736c0ddc37fb048981aeb950e694889c5b890963fb1bc31a773cb87016cc9877b7db84c7c892a66bfb0fb08a11a8aaf2a9d6cd97d8f02b8947c222db8b8c5e1888f4e161ce5ada916e029f966494c25b5ba3530ab8969d9eb611c11551e93bd4215ce8a7df3b49e0a60d384334f97baa79581f2d30a82e3bd29952787ae5de946b5b436db85a2eee7778027537f56e9d9f90e52d74d97a5f020bb2f47385cd7bd05d5ebbf2c737ba43e94fd8bf4560cea7556f1b2d293c7a479372f99ce05eae9d4b879ffeef6eff7d7576ffffab8f4217d71a29f974ff1db92f469f5ff61f4ff07504b070839044206d9060000c8310000504b030414000008000087615141f17c5adc7708000077080000180000005468756d626e61696c732f7468756d626e61696c2e706e6789504e470d0a1a0a0000000d49484452000000b50000010008020000007a41a08c0000083e49444154789cedda4b48557b1bc77135092f5d2d1443b330b2b2ac41a14438a8176952126f5883a22848ac46065da088a851839a44b36e742138451035e842601a11113a29a530759029da4d0dc332df071fde75d6bbaddff1ece37ef574be9fc162ddfcffd7daebbbb766260e0c0cc4013f9138da1780318d3ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d0079431d1c7f9f3e7d7ac59336ddab4bf3e545555556161615252d25f1f6a748d911b896d1fc78e1d3b78f0a02f2b2b2bd3d2d26edebcf9dba0efdfbf3f79f2e4c8912367cf9eb5fd4d4d4d67ce9cb13dddddddc5c5c5ab57af5eba74e9c993279b9b9bb76ddb76e1c2053bc7765eb97265f2e4c9595959ff1e74fffefdf8f8781bd95ec7152b56d8a69d969a9a6aa7d9caa64d9bec0bdbdbdbf7eddb67337efefcf9d3a74f3e8e0dee5765176917d0d2d262fb6de983777575f9bc8b172f0e067ffaf4e9e1c387ed826d695f75f1e2c5bababa254b96bc7efddaf7676767fb381f3f7eb4abb2cd9c9c9c952b57465c5ec4ec7672702311f76b373267ce1c3fba6bd72ebf8b8c8c0c9fb7b6b636182d764f30b67db4b5b5d9d2eecd961d1d1d763f53a74e3d7efcf8c48913f7efdf6f8f61e1c285be7fd2a4493b76ecb03df612dbc9b6694b7b2d5ebc78919898189cd3d9d9d9d3d3638fedf2e5cbf6daddbb77afa4a4a4afafefc4891315151576c84ff3f33333336d65dcb8713ea31df53e7c70bf36535d5d7deedcb99d3b77f60cb2c19393937d5e3b1a0cee9b7601fe558f1f3f3e7dfa747f7fffd6ad5bed61db44f6687d9c848404bb97b2b2b2868686f008c1e585670fdfc8d0fb0d8e06776139fabc77efde0d468b9dd8f6611f06f6daad5fbfdeb2f03dbdbdbdf696aaa9a98938d35ef7f4f4f4f0e6dcb973ebebeb5352521a1b1b839d9ed1d5ab575b5b5bedad73e3c60d7b59eda91f3a74a8a8a8c8de677e9a7d0cf8bbd3d8bb70d9b265e1197df0e0daf2f2f20e1c38602fbd3f571bdc1e86cf9b9f9f1f0c6e9f707bf7eeb5a07d90dcdc5cdbb4aff5b976efde1d8c631f54f608cbcbcbaf5fbf6eeffea197179edd3e96821b197abfc1519bc5ef2298377c2f23fad0fe476cfbb0cfc960dd3e906de98fcd5eb5b8c1ef3ec17ee77b02478f1efdf6ed9bbd9fd6ae5deb7b962f5f6ecb828202dfdcb8716378842d5bb6844f33a74e9df2159ff187d7669f64f676b4267cd307f779232e2f7cda9e3d7bec5ba425159e287c42c48d872f2f3cbbfdec15dcc8cfeed78f0677e1f36edfbe3d2ef6c6c4cfa7823fa4581bfa507f386fc469411c629c3f6b38f73b74ded819eb7d6074d1c7efae5dbbe62bf663c1e85ec9d8411fbf238ba1e8030a7d40a10f28d1f7d1dbdb9b9c9ceccb619e6c2b030303f1f1f1c1fe88cd3f3c7f44fca92bff878ba68f478f1ee5e4e4747676767777777474cc9c3973faf4e94d4d4df9f9f9cf9f3fb7e5cb972fed506e6eee9b376f6c333d3dddbe64ca9429765a7575f5fcf9f36da5b5b5352b2babaaaaca3767cd9a75e7ce1dfbf2cacaca9a9a1a1b363b3bdbcfdfbc79b3ed494a4af2df25777575d9fe2f5fbe5837765a6666667f7fbfedf7b95a5a5a525353131212eca85d834f949696f6f6eddbe0685f5fdf870f1fbe7efd5a525262fb6d84117e517f21d1f4d1d6d63663c60cebc31e4f4a4acac3870f0b0a0aac097b3bd6d7d7dbd21ed5ad5bb7ec9f03b6629bfe8b733fcd9eb1afbc7af5ca1e5eb0697d343737b7b7b7c70d3ee9dada5a0bcecff719ed91db3b7ec284098d8d8db6df4e2e2d2dadababebe9e979f7ee9dedf7b99e3d7b6621da9976f4f6eddb76a6edb1c21e3c78101ccdc8c8b0ab7dfffebdf5f1fffc5dd3df51347df86f7967cf9e1ddeb96ad52a7bcb161616dab2a8a8a8acaccc5682ef0eff1a143edf37c3ff77505c5c5c5e5e6e2bebd6ad5bb468d182050b8243965a4343435e5e5ec4f79a0d1b36c4fdf77b902ffd02fca87d140513555454848ffad5c60dfe176014afc03fc788fd7cea2f77f06c223687c3bed1f8caf8f1e3c371b879f3e60d67ea9fcdf8c3cbc31fe2df2f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d4089a68f61fe0d077e01d1f471e9d2a59ffd0dc7485f1e4659347d88bfe118e9cbc3288ba68fd2d2525f19fa371cf8c5f0f32914fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e8030a7d40a10f28f401853ea0d00714fa80421f50e803ca7f00604231b2b26910650000000049454e44ae426082504b0304140008080800876151410000000000000000000000000c0000006c61796f75742d63616368656364606428e063606008e0646060e1053200504b070888735f5c1200000012000000504b0304140008080800876151410000000000000000000000000c0000006d616e69666573742e726466cd93cd6e83301084ef3c8565ced8402f05057228cab96a9fc0358658052ff29a12debe8e935651a4aaea9fd4e3ae4633df8eb49bed611cc88bb2a8c15434632925ca4868b5e92b3abb2eb9a5db3adad8b62b1f9a1df16a83a59f2aba776e2a395f96852d370c6ccfb3a228789af33c4fbc22c1d53871480cc6b48e08091e8d4269f5e47c1a39cee20966575174eba09079f7203d8bdd3aa9a0b20a61b652bd87b6209181408d094cca8474831cba4e4bc53396f35139c1a1ede2c760bdd383a23c60f02b8ecfd8de880ca6e55ee0bdb0ee5c83df7c95687aee637a75d3c5f1df2394609c32ee4feabb3b79ffe7fe2ecfff19e2afb476446c40cea367fa90e7b4f21f5547af504b0708b4f768d20501000083030000504b0304140000080000876151410000000000000000000000001f000000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b0304140000080000876151410000000000000000000000001a000000436f6e66696775726174696f6e73322f706f7075706d656e752f504b0304140000080000876151410000000000000000000000001a000000436f6e66696775726174696f6e73322f746f6f6c70616e656c2f504b0304140000080000876151410000000000000000000000001a000000436f6e66696775726174696f6e73322f7374617475736261722f504b0304140000080000876151410000000000000000000000001c000000436f6e66696775726174696f6e73322f70726f67726573736261722f504b03041400000800008761514100000000000000000000000018000000436f6e66696775726174696f6e73322f746f6f6c6261722f504b03041400000800008761514100000000000000000000000018000000436f6e66696775726174696f6e73322f6d656e756261722f504b03041400000800008761514100000000000000000000000018000000436f6e66696775726174696f6e73322f666c6f617465722f504b03041400080808008761514100000000000000000000000027000000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c0300504b0708000000000200000000000000504b0304140008080800876151410000000000000000000000000a0000007374796c65732e786d6ced5c5b8fdb36167edf5f61a8e8bed196ecb9d99b4911140876814e3648d2e78296685b8d240a243db6f3eb7b78932899b2e5f18c51cc340f0398e7903cfcce95149977bf6cf36cf048184f69711f44c330189022a6495a2cef83dfbf7d4477c12feffff58e2e16694c66098dd7392904e26297113e80ce059f69e27db066c58c629ef2598173c267229ed19214b6d3cce59ea9a9748b1aac6f77c5ecf616642bfa7696bc8dbe78de7f66c5ecf64e18def4ed2c790153b7fb82f6edbce5195a5014d3bcc4226d49b1cdd2e2fb7db012a29c8d469bcd66b8990c295b8ea2e9743a52d44ae0b8e22bd72c535c493c22199193f151348c4696372702f7954ff2ba2215eb7c4e586f68b0c07b5ae58fcbde16f1b8ec80265e61d6db36147353bd93a4bf7a2789db37c762d5a193bbd10310d59f87df6a5b6079dfb9246f03aa98a565ef656a6eb73fa5b4125576d00eaac41d87e1d548ff76b83707d9372c158439ecf141f61867718538cd7da0015f34020e441ea5995a6e2617dd39f2f5889192325109b2e81fa0009d71e55e2b9167ddee25a99675c992c4cb0ae24c46e06a60e8e831259b9f1af1e730fed39162aa4c9af389f0cdf1edcb48d2900c71e0c426ca3a917d1cbcb7617c4121842f704c5042e28cbf7fa7ddaf6a1ee8df12a4fbe01b5ed11c47c100fcccb2e469b6b3946074a47f0a500f3e91cde00b70179e71fe8d4bcaffd3e2d38dc1a031b4e4474b521096825531335ecd51a62206bf7bc42c55a01d11ed03b0651e816c7bf7d47c93727ef6d43e50f7e7c60965850ad0f7c127f0fd8b8836f8bd48a10c208387af9d1a6b33f65019df7141f273a43346d7698de7cf3eeaf212d3ae6b1f2b654216789d998ac88e6c445a325caed238b0bce6372a19f8391329d8fb82ce36d08a6829949f1614c9dfc140160c33be02e56f104ccc8940dbfb201c4ee2dc4bdcb58802b21982e44f102f710ca5075a5196fe8035e14cb28eef0e323f4af9e27d5688967d47dd63f58c69f0ca601d9b54ac902ee61638e38e21949861059d0b9c26497e84d782ca39c03ad28450cd8ab3725559831263ce088642890bb0056129325b49d97230e1fb206348cc1bf691160991294216bdee62ac90564608d26002b4e4d280bac5aed8a5dc7bab597302301452ab6af2986614ca28c1d610ffc15254234f7f80a4d1b814aa2dc3c5728d97d0b460aa21a6eb423030878f5faae5130129197d27ac50a2eb019d55ca311124465cd8914d4733b8a5fdd86e2dc94c6329052d3c43cab22c23db8e412baa67d88aa606ae416db85b1f1facb4101c3427006eb52b57a4c0d20f518693041053d228a7ccd23cad56d0d3eaca75118bb51e503a35a46d583a68e0b8595a7342490ace59c84920835f47b5cf340db7043c6b87798275399aeb48db2f697f724a6b499ef4f3a286aae6aecccd93459ed5925d43233688b4ad8f911ca705923b126b82e33da672cd572d9633dc4417994e34cb886b417aef3ba74c7a85343908e2603f192eb9b4e77327468c6e5a93434bcb3fbf135222419744ace4e652fadfb189dd09b5597f056f4a304b82ce3061d597610e059774a5dab1f6c7fb2fc189e3d19dc34143756e82fca214d26b5d866fd0f0c738fc634e939d4fac63012dc70ca20d4056ca8c7b355619b76e9f5321e4860b927134362485b1cac485cac438dbe01d3f16599cb061cad756b0b8aa7de60c7ff78e7282e7cafeb5c11c31141ff8108ccb0cef1cf50c5cf239ca7fb25e0febb4f7727f833cf394751cb0d14c0ed9d370da6a8cfa4bfe2b56a5f3332a014465f844efaa5da8cbbbd4d9a33e19539524afa2bfa2a85ac01e9c85dd1ed79186759bec01e50814e3b0ebe89337eb6e96eae9ec5750bf14e94c50d1cd14bd35fc3f28bfb7cfa8df548d7750bfcfa4ace7b3f20fb1206bc63f33c2f519d819689c16cca3bd681e9e12478de00f726e419e24787bed07d2b161fd307f324aa74ff62b40c3c8197359697bad4b00eb13c35d53de03b3e5b490319dd31cead07a69cdf4074c8336c729d2b42a275bbfd1b550de9591475964567126c75cee21e446c70cf02c79b2de50e9918d4fdbe2b2570c8e4ef107b36c896fe407d6700ca26740f5c935e5b1255fff5c87850d49972b2816e7344bbab38eeae210752f4bf6f7ad534a47ef8a41f5ef5f33c010d2e50e68c1b23c4d0d8d94250ba106f593b2b2b615f4cf4e951dcb601cfeec1a764616722bd8b476a6f5d3f281c3f56355cf284b48d509d87d80601f319dd64e23fd0435385ee058ec78463ac9033f52aa3e505db25e3cad9ed843afdd6045a43cd5c7c677a096bb5a2d625782b831c80f0b1d1ded1ddd0ca7ae56757765350eac8e340755d94f0b0d1ff8bacbc181b9df132bce418bad3a42307b7405a5cd1ffb33fe5f538216a34a34865d2ba39579402f487e73c5c2c939caa775d7b69a5d92811881dda89363620f9af11cc850062fd5971ddfb02d1633b66a5cd02ca31b92a0f94e6fb34035813379757a58ab381cdede7479744d6904114ba82dc0bb68a076e1d913e9f1ab423a1a86d18d176997d240da125e1ce9c92b437a7cdb01744568e1acda5f1ce6ab5706f3f5f8aa03e79ad2025a135e1ce9eb5786f4eded5d07d235a585b426bc38d237af0ae9f1309cf8d3a14b69d6d486f0e248dfbe32a4c777fe74e8525a486bc28b237df7ca90bef6876987d0c2f9fa22417afaca60be9d76e15c535a406bc28b231d85af0aeac930bcf2e74397d280da12ce81ba4972f1af4f72baf7b21de749f52e360adcd19c99cd0940f72e94af178b747b1f0cd060e05374f437d47441c54a1d48761f25ed6f3c35a1a70e3b603c01e47117c8af19e5e892084fde24c297b6e3abb788f20935f9f9085fbf49842f6dc7376f11e5c92511be7d93080faf2f89f1dd5bc4f86a188617ad2ca66f13e58b5a72735bf95640be7e265376c8ee5e1224221cc5b458a4cb355377ba071501994fcb0b4a85fced8759f3ebd73b8f385b13f9f15837da8edcd94eab47026e9fc2ee49911ccf3e83ac3ea5f6929014499780a95fc051657905a925f04dd3f9bd5cbf79aa778a07d03183d42848e51a5a5ac44c3d7d96859df3f24b8d563ff892971c60cc344696a0a7db3f0878f8fca4db22e646c2b13b1fca6e95a957dff6cd987faeb948173b242fe3c3a81bca92bd5754aaf7e957b7c3f65d4ef74e8f97585fda39705150ddb6caf08eae4513c1328f020f530b0a45d9a4897c653d0eeded054b58990b49e3e9f0e6807d9849c0fc04a22c0544b17114ca04c3a97062c2de9d1a757ba6e3ceaffffb81f74eceb8eb454feb299af66694e36db53a7975a77e2b691838b1d73b0c3ae1308c9c9b21f63d119a134042f1abdb3e61e4e1c10b79e5cec7529f8c355fd4e0449aa27629ed68ba9d4154b4eaaa6f729917483f85ea5fe0de11f229de2e7645b07c86a27e8c5c046c638bd1905a17a8400715926040ded3c4d0ab33ef3daa66244a7660ce3260345f0bd62b6c8abbbfec1e7e32fe3bfa8946231adedcdcfa7d231a46fe6fc6fff8c7c5fcc33b509dfef6f39d21983bb8cd2ce85ccc6d5868fbb29c2bb327d66bf94c6950dac2a1ce9132b55a6a7d53d7f0918cc44289009971cd982a16c7a6f272d86d3156b6fcb0fae92ce6d8fa3ea68cababb49f555955998673e6af58062edd0b427dfc4cb61d170e9de2a4a58491ffbfe679ff17504b0708cfbc7478570a0000da470000504b030414000808080087615141000000000000000000000000150000004d4554412d494e462f6d616e69666573742e786d6cad544b6ec32010dde71416dbcad06655a1385944ea09d2034cf0d84182c182218a6f5f3b6a3e5595ca56b3637eefbd6106569b9377c51163b2812af1265f458164426da9adc4e7eea37c179bf562e5816c8389f5e5500c7594ae662572241d20d9a4093c26cd46870ea90e267b24d63ff3f599e96add09588af5a2b8f135d66139d4c7fe96dd64e7ca0ef85009f508e4e6f6585b28b9efb012d075ce1ae0214d1da99667c1f25ea7643cb1507334786490437b0f3847403586678126641e66909e0e6c02f1d8e7b3717787ecf704d625c597a3eca87d40623db4a8c6f82c16077dc85c1a30079c30e3bd2588fdcc717efb64ac9b091443d6cbeccbda066a6c9be319222d1518830e07334465728c7f0fe87f5c139f46ca344a90d94a738f30738db97738758957ead737b3fe02504b070890c242f02d010000a1040000504b01021400140000080000876151415ec6320c27000000270000000800000000000000000000000000000000006d696d6574797065504b0102140014000008000087615141781176c4e5030000e503000008000000000000000000000000004d0000006d6574612e786d6c504b0102140014000808080087615141963464fa6a0500002f2300000c000000000000000000000000005804000073657474696e67732e786d6c504b010214001400080808008761514139044206d9060000c83100000b00000000000000000000000000fc090000636f6e74656e742e786d6c504b0102140014000008000087615141f17c5adc770800007708000018000000000000000000000000000e1100005468756d626e61696c732f7468756d626e61696c2e706e67504b010214001400080808008761514188735f5c12000000120000000c00000000000000000000000000bb1900006c61796f75742d6361636865504b0102140014000808080087615141b4f768d205010000830300000c00000000000000000000000000071a00006d616e69666573742e726466504b01021400140000080000876151410000000000000000000000001f00000000000000000000000000461b0000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b01021400140000080000876151410000000000000000000000001a00000000000000000000000000831b0000436f6e66696775726174696f6e73322f706f7075706d656e752f504b01021400140000080000876151410000000000000000000000001a00000000000000000000000000bb1b0000436f6e66696775726174696f6e73322f746f6f6c70616e656c2f504b01021400140000080000876151410000000000000000000000001a00000000000000000000000000f31b0000436f6e66696775726174696f6e73322f7374617475736261722f504b01021400140000080000876151410000000000000000000000001c000000000000000000000000002b1c0000436f6e66696775726174696f6e73322f70726f67726573736261722f504b01021400140000080000876151410000000000000000000000001800000000000000000000000000651c0000436f6e66696775726174696f6e73322f746f6f6c6261722f504b010214001400000800008761514100000000000000000000000018000000000000000000000000009b1c0000436f6e66696775726174696f6e73322f6d656e756261722f504b01021400140000080000876151410000000000000000000000001800000000000000000000000000d11c0000436f6e66696775726174696f6e73322f666c6f617465722f504b01021400140008080800876151410000000002000000000000002700000000000000000000000000071d0000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c504b0102140014000808080087615141cfbc7478570a0000da4700000a000000000000000000000000005e1d00007374796c65732e786d6c504b010214001400080808008761514190c242f02d010000a10400001500000000000000000000000000ed2700004d4554412d494e462f6d616e69666573742e786d6c504b05060000000012001200aa0400005d2900000000	2012-11-16 14:59:09	2012-11-16 15:04:45	1
\.

--
-- Name: models_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modeltemplates_id_seq', 1, true);


--
-- Data for Name: modeltypes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY modeltypes (id, name, description, created, modified) FROM stdin;
1	Toutes Editions	Toutes Editions	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
2	Projet	Projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
3	Délibération	Délibération	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
4	Convocation	Convocation	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
5	Ordre du jour	Ordre du jour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
6	PV sommaire	PV sommaire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
7	PV détaillé	PV détaillé	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
8	Recherche	Recherche	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
9	Multi-séance	Multi-séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
\.


--
-- Name: modeltypes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modeltypes_id_seq', 10, false);


--
-- Data for Name: modelvalidations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) FROM stdin;
1	\N	2	1	0	1	t
2	\N	3	1	1	1	t
3	\N	4	1	0	1	t
4	\N	5	1	0	2	t
5	\N	4	2	0	1	t
6	\N	5	2	0	2	t
7	\N	4	3	0	1	t
8	\N	5	3	0	2	t
9	\N	2	4	0	1	t
10	\N	5	4	0	2	t
11	\N	2	5	1	1	t
12	\N	4	5	0	1	t
13	\N	5	5	0	2	t
14	\N	2	6	1	1	t
15	\N	4	6	0	1	t
16	\N	5	6	0	2	t
17	\N	2	7	1	1	t
18	\N	4	7	0	1	t
19	\N	5	7	0	2	t
20	\N	2	8	1	1	t
21	\N	4	8	0	1	t
22	\N	5	8	0	2	t
23	\N	2	9	0	1	t
24	\N	3	9	1	1	t
25	\N	4	9	0	1	t
26	\N	5	9	0	2	t
27	1	1	2	0	0	t
28	1	1	4	0	0	t
29	1	1	5	0	0	t
30	1	1	3	0	0	t
31	1	1	6	0	0	t
32	1	1	7	0	0	t
33	1	1	8	0	0	t
34	1	1	9	0	0	t
35	2	1	2	0	0	t
36	2	1	4	0	0	t
37	2	1	5	0	0	t
38	2	1	3	0	0	t
39	2	1	6	0	0	t
40	2	1	7	0	0	t
41	2	1	8	0	0	t
42	2	1	9	0	0	t
43	3	1	2	0	0	t
44	3	1	4	0	0	t
45	3	1	5	0	0	t
46	3	1	3	0	0	t
47	3	1	6	0	0	t
48	3	1	7	0	0	t
49	3	1	8	0	0	t
50	3	1	9	0	0	t
51	4	1	2	0	0	t
52	4	1	4	0	0	t
53	4	1	5	0	0	t
54	4	1	3	0	0	t
55	4	1	6	0	0	t
56	4	1	7	0	0	t
57	4	1	8	0	0	t
58	4	1	9	0	0	t
59	5	1	2	0	0	t
60	5	1	4	0	0	t
61	5	1	5	0	0	t
62	5	1	3	0	0	t
63	5	1	6	0	0	t
64	5	1	7	0	0	t
65	5	1	8	0	0	t
66	5	1	9	0	0	t
67	6	1	2	0	0	t
68	6	1	4	0	0	t
69	6	1	5	0	0	t
70	6	1	3	0	0	t
71	6	1	6	0	0	t
72	6	1	7	0	0	t
73	6	1	8	0	0	t
74	6	1	9	0	0	t
75	7	1	2	0	0	t
76	7	1	4	0	0	t
77	7	1	5	0	0	t
78	7	1	3	0	0	t
79	7	1	6	0	0	t
80	7	1	7	0	0	t
81	7	1	8	0	0	t
82	7	1	9	0	0	t
83	8	1	2	0	0	t
84	8	1	4	0	0	t
85	8	1	5	0	0	t
86	8	1	3	0	0	t
87	8	1	6	0	0	t
88	8	1	7	0	0	t
89	8	1	8	0	0	t
90	8	1	9	0	0	t
91	9	1	2	0	0	t
92	9	1	4	0	0	t
93	9	1	5	0	0	t
94	9	1	3	0	0	t
95	9	1	6	0	0	t
96	9	1	7	0	0	t
97	9	1	8	0	0	t
98	9	1	9	0	0	t
99	10	1	2	0	0	t
100	10	1	4	0	0	t
101	10	1	5	0	0	t
102	10	1	3	0	0	t
103	10	1	6	0	0	t
104	10	1	7	0	0	t
105	10	1	8	0	0	t
106	10	1	9	0	0	t
107	11	1	2	0	0	t
108	11	1	4	0	0	t
109	11	1	5	0	0	t
110	11	1	3	0	0	t
111	11	1	6	0	0	t
112	11	1	7	0	0	t
113	11	1	8	0	0	t
114	11	1	9	0	0	t
115	12	1	2	0	0	t
116	12	1	4	0	0	t
117	12	1	5	0	0	t
118	12	1	3	0	0	t
119	12	1	6	0	0	t
120	12	1	7	0	0	t
121	12	1	8	0	0	t
122	12	1	9	0	0	t
123	13	1	2	0	0	t
124	13	1	4	0	0	t
125	13	1	5	0	0	t
126	13	1	3	0	0	t
127	13	1	6	0	0	t
128	13	1	7	0	0	t
129	13	1	8	0	0	t
130	13	1	9	0	0	t
131	14	1	2	0	0	t
132	14	1	4	0	0	t
133	14	1	5	0	0	t
134	14	1	3	0	0	t
135	14	1	6	0	0	t
136	14	1	7	0	0	t
137	14	1	8	0	0	t
138	14	1	9	0	0	t
139	15	1	2	0	0	t
140	15	1	4	0	0	t
141	15	1	5	0	0	t
142	15	1	3	0	0	t
143	15	1	6	0	0	t
144	15	1	7	0	0	t
145	15	1	8	0	0	t
146	15	1	9	0	0	t
147	16	1	2	0	0	t
148	16	1	4	0	0	t
149	16	1	5	0	0	t
150	16	1	3	0	0	t
151	16	1	6	0	0	t
152	16	1	7	0	0	t
153	16	1	8	0	0	t
154	16	1	9	0	0	t
155	17	1	2	0	0	t
156	17	1	4	0	0	t
157	17	1	5	0	0	t
158	17	1	3	0	0	t
159	17	1	6	0	0	t
160	17	1	7	0	0	t
161	17	1	8	0	0	t
162	17	1	9	0	0	t
163	18	1	2	0	0	t
164	18	1	5	0	0	t
165	18	1	3	0	0	t
166	18	1	6	0	0	t
167	18	1	7	0	0	t
168	18	1	8	0	0	t
169	18	1	9	0	0	t
170	19	1	2	0	0	t
171	19	1	5	0	0	t
172	19	1	3	0	0	t
173	19	1	6	0	0	t
174	19	1	7	0	0	t
175	19	1	8	0	0	t
176	19	1	9	0	0	t
177	20	1	2	0	0	t
178	20	1	5	0	0	t
179	20	1	3	0	0	t
180	20	1	6	0	0	t
181	20	1	7	0	0	t
182	20	1	8	0	0	t
183	20	1	9	0	0	t
184	21	1	2	0	0	t
185	21	1	5	0	0	t
186	21	1	3	0	0	t
187	21	1	6	0	0	t
188	21	1	7	0	0	t
189	21	1	8	0	0	t
190	21	1	9	0	0	t
191	22	1	2	0	0	t
192	22	1	5	0	0	t
193	22	1	3	0	0	t
194	22	1	6	0	0	t
195	22	1	7	0	0	t
196	22	1	8	0	0	t
197	22	1	9	0	0	t
198	23	1	2	0	0	t
199	23	1	5	0	0	t
200	23	1	3	0	0	t
201	23	1	6	0	0	t
202	23	1	7	0	0	t
203	23	1	8	0	0	t
204	23	1	9	0	0	t
205	24	1	2	0	0	t
206	24	1	5	0	0	t
207	24	1	3	0	0	t
208	24	1	6	0	0	t
209	24	1	7	0	0	t
210	24	1	8	0	0	t
211	24	1	9	0	0	t
212	25	1	2	0	0	t
213	25	1	5	0	0	t
214	25	1	3	0	0	t
215	25	1	6	0	0	t
216	25	1	7	0	0	t
217	25	1	8	0	0	t
218	25	1	9	0	0	t
219	26	1	2	0	0	t
220	26	1	5	0	0	t
221	26	1	3	0	0	t
222	26	1	6	0	0	t
223	26	1	7	0	0	t
224	26	1	8	0	0	t
225	26	1	9	0	0	t
226	27	1	2	0	0	t
227	27	1	5	0	0	t
228	27	1	3	0	0	t
229	27	1	6	0	0	t
230	27	1	7	0	0	t
231	27	1	8	0	0	t
232	27	1	9	0	0	t
233	28	1	2	0	0	t
234	28	1	5	0	0	t
235	28	1	3	0	0	t
236	28	1	6	0	0	t
237	28	1	7	0	0	t
238	28	1	8	0	0	t
239	28	1	9	0	0	t
240	29	1	2	0	0	t
241	29	1	5	0	0	t
242	29	1	3	0	0	t
243	29	1	6	0	0	t
244	29	1	7	0	0	t
245	29	1	8	0	0	t
246	29	1	9	0	0	t
247	30	1	2	0	0	t
248	30	1	5	0	0	t
249	30	1	3	0	0	t
250	30	1	6	0	0	t
251	30	1	7	0	0	t
252	30	1	8	0	0	t
253	30	1	9	0	0	t
254	31	1	2	0	0	t
255	31	1	5	0	0	t
256	31	1	3	0	0	t
257	31	1	6	0	0	t
258	31	1	7	0	0	t
259	31	1	8	0	0	t
260	31	1	9	0	0	t
261	32	1	2	0	0	t
262	32	1	5	0	0	t
263	32	1	3	0	0	t
264	32	1	6	0	0	t
265	32	1	7	0	0	t
266	32	1	8	0	0	t
267	32	1	9	0	0	t
268	33	1	2	0	0	t
269	33	1	5	0	0	t
270	33	1	3	0	0	t
271	33	1	6	0	0	t
272	33	1	7	0	0	t
273	33	1	8	0	0	t
274	33	1	9	0	0	t
275	34	1	2	0	0	t
276	34	1	5	0	0	t
277	34	1	3	0	0	t
278	34	1	6	0	0	t
279	34	1	7	0	0	t
280	34	1	8	0	0	t
281	34	1	9	0	0	t
282	35	1	2	0	0	t
283	35	1	5	0	0	t
284	35	1	3	0	0	t
285	35	1	6	0	0	t
286	35	1	7	0	0	t
287	35	1	8	0	0	t
288	35	1	9	0	0	t
289	36	1	2	0	0	t
290	36	1	5	0	0	t
291	36	1	3	0	0	t
292	36	1	6	0	0	t
293	36	1	7	0	0	t
294	36	1	8	0	0	t
295	36	1	9	0	0	t
296	37	1	2	0	0	t
297	37	1	5	0	0	t
298	37	1	3	0	0	t
299	37	1	6	0	0	t
300	37	1	7	0	0	t
301	37	1	8	0	0	t
302	37	1	9	0	0	t
303	38	1	2	0	0	t
304	38	1	4	0	0	t
305	38	1	5	0	0	t
306	38	1	3	0	0	t
307	38	1	6	0	0	t
308	38	1	7	0	0	t
309	38	1	8	0	0	t
310	38	1	9	0	0	t
311	39	1	2	0	0	t
312	39	1	4	0	0	t
313	39	1	5	0	0	t
314	39	1	3	0	0	t
315	39	1	6	0	0	t
316	39	1	7	0	0	t
317	39	1	8	0	0	t
318	39	1	9	0	0	t
319	40	1	2	0	0	t
320	40	1	4	0	0	t
321	40	1	5	0	0	t
322	40	1	3	0	0	t
323	40	1	6	0	0	t
324	40	1	7	0	0	t
325	40	1	8	0	0	t
326	40	1	9	0	0	t
327	41	1	2	0	0	t
328	41	1	4	0	0	t
329	41	1	5	0	0	t
330	41	1	3	0	0	t
331	41	1	6	0	0	t
332	41	1	7	0	0	t
333	41	1	8	0	0	t
334	41	1	9	0	0	t
335	42	1	2	0	0	t
336	42	1	4	0	0	t
337	42	1	5	0	0	t
338	42	1	3	0	0	t
339	42	1	6	0	0	t
340	42	1	7	0	0	t
341	42	1	8	0	0	t
342	42	1	9	0	0	t
343	43	1	2	0	0	t
344	43	1	4	0	0	t
345	43	1	5	0	0	t
346	43	1	3	0	0	t
347	43	1	6	0	0	t
348	43	1	7	0	0	t
349	43	1	8	0	0	t
350	43	1	9	0	0	t
351	44	1	2	0	0	t
352	44	1	4	0	0	t
353	44	1	5	0	0	t
354	44	1	3	0	0	t
355	44	1	6	0	0	t
356	44	1	7	0	0	t
357	44	1	8	0	0	t
358	44	1	9	0	0	t
359	45	1	2	0	0	t
360	45	1	4	0	0	t
361	45	1	5	0	0	t
362	45	1	3	0	0	t
363	45	1	6	0	0	t
364	45	1	7	0	0	t
365	45	1	8	0	0	t
366	45	1	9	0	0	t
367	46	1	2	0	0	t
368	46	1	5	0	0	t
369	46	1	3	0	0	t
370	46	1	6	0	0	t
371	46	1	7	0	0	t
372	46	1	8	0	0	t
373	46	1	9	0	0	t
374	47	1	2	0	0	t
375	47	1	5	0	0	t
376	47	1	3	0	0	t
377	47	1	6	0	0	t
378	47	1	7	0	0	t
379	47	1	8	0	0	t
380	47	1	9	0	0	t
381	48	1	2	0	0	t
382	48	1	5	0	0	t
383	48	1	3	0	0	t
384	48	1	6	0	0	t
385	48	1	7	0	0	t
386	48	1	8	0	0	t
387	48	1	9	0	0	t
388	49	1	2	0	0	t
389	49	1	5	0	0	t
390	49	1	3	0	0	t
391	49	1	6	0	0	t
392	49	1	7	0	0	t
393	49	1	8	0	0	t
394	49	1	9	0	0	t
395	50	4	2	0	0	t
396	50	4	3	0	0	t
397	50	4	6	0	0	t
398	50	4	7	0	0	t
399	50	4	8	0	0	t
400	50	4	9	0	0	t
401	48	4	2	0	0	t
402	48	4	3	0	0	t
403	48	4	6	0	0	t
404	48	4	7	0	0	t
405	48	4	8	0	0	t
406	48	4	9	0	0	t
407	51	5	2	0	0	t
408	51	5	3	0	0	t
409	51	5	6	0	0	t
410	51	5	7	0	0	t
411	51	5	8	0	0	t
412	51	5	9	0	0	t
413	52	5	2	0	0	t
414	52	5	3	0	0	t
415	52	5	6	0	0	t
416	52	5	7	0	0	t
417	52	5	8	0	0	t
418	52	5	9	0	0	t
419	53	5	2	0	0	t
420	53	5	3	0	0	t
421	53	5	6	0	0	t
422	53	5	7	0	0	t
423	53	5	8	0	0	t
424	53	5	9	0	0	t
425	54	5	2	0	0	t
426	54	5	3	0	0	t
427	54	5	6	0	0	t
428	54	5	7	0	0	t
429	54	5	8	0	0	t
430	54	5	9	0	0	t
431	55	1	4	0	0	t
432	55	1	5	0	0	t
433	55	1	8	0	0	t
434	55	1	9	0	0	t
435	56	1	4	0	0	t
436	56	1	5	0	0	t
437	56	1	8	0	0	t
438	56	1	9	0	0	t
439	57	1	4	0	0	t
440	57	1	5	0	0	t
441	57	1	8	0	0	t
442	57	1	9	0	0	t
443	58	1	4	0	0	t
444	58	1	5	0	0	t
445	58	1	8	0	0	t
446	58	1	9	0	0	t
447	59	1	4	0	0	t
448	59	1	5	0	0	t
449	59	1	8	0	0	t
450	59	1	9	0	0	t
451	60	1	4	0	0	t
452	60	1	5	0	0	t
453	60	1	8	0	0	t
454	60	1	9	0	0	t
455	61	1	4	0	0	t
456	61	1	5	0	0	t
457	61	1	8	0	0	t
458	61	1	9	0	0	t
459	62	1	4	0	0	t
460	62	1	5	0	0	t
461	62	1	8	0	0	t
462	62	1	9	0	0	t
463	63	1	4	0	0	t
464	63	1	5	0	0	t
465	63	1	8	0	0	t
466	63	1	9	0	0	t
467	64	1	4	0	0	t
468	64	1	5	0	0	t
469	64	1	8	0	0	t
470	64	1	9	0	0	t
471	65	1	4	0	0	t
472	65	1	5	0	0	t
473	65	1	8	0	0	t
474	65	1	9	0	0	t
475	66	1	4	0	0	t
476	66	1	5	0	0	t
477	66	1	8	0	0	t
478	66	1	9	0	0	t
479	67	1	5	0	0	t
480	67	1	6	0	0	t
481	67	1	7	0	0	t
482	67	1	8	0	0	t
483	67	1	9	0	0	t
484	68	1	5	0	0	t
485	68	1	6	0	0	t
486	68	1	7	0	0	t
487	68	1	8	0	0	t
488	68	1	9	0	0	t
489	69	1	5	0	0	t
490	69	1	6	0	0	t
491	69	1	7	0	0	t
492	69	1	8	0	0	t
493	69	1	9	0	0	t
494	70	1	3	0	0	t
495	70	1	6	0	0	t
496	70	1	7	0	0	t
497	70	1	8	0	0	t
498	70	1	9	0	0	t
499	71	1	3	0	0	t
500	71	1	6	0	0	t
501	71	1	7	0	0	t
502	71	1	8	0	0	t
503	71	1	9	0	0	t
504	72	1	3	0	0	t
505	72	1	6	0	0	t
506	72	1	7	0	0	t
507	72	1	8	0	0	t
508	72	1	9	0	0	t
509	73	1	3	0	0	t
510	73	1	6	0	0	t
511	73	1	7	0	0	t
512	73	1	8	0	0	t
513	73	1	9	0	0	t
514	74	1	3	0	0	t
515	74	1	6	0	0	t
516	74	1	7	0	0	t
517	74	1	8	0	0	t
518	74	1	9	0	0	t
519	75	1	3	0	0	t
520	75	1	6	0	0	t
521	75	1	7	0	0	t
522	75	1	8	0	0	t
523	75	1	9	0	0	t
524	76	1	3	0	0	t
525	76	1	6	0	0	t
526	76	1	7	0	0	t
527	76	1	8	0	0	t
528	76	1	9	0	0	t
529	77	1	3	0	0	t
530	77	1	6	0	0	t
531	77	1	7	0	0	t
532	77	1	8	0	0	t
533	77	1	9	0	0	t
534	78	1	3	0	0	t
535	78	1	6	0	0	t
536	78	1	7	0	0	t
537	78	1	8	0	0	t
538	78	1	9	0	0	t
539	79	1	3	0	0	t
540	79	1	6	0	0	t
541	79	1	7	0	0	t
542	79	1	8	0	0	t
543	79	1	9	0	0	t
544	80	1	3	0	0	t
545	80	1	6	0	0	t
546	80	1	7	0	0	t
547	80	1	8	0	0	t
548	80	1	9	0	0	t
549	81	1	3	0	0	t
550	81	1	6	0	0	t
551	81	1	7	0	0	t
552	81	1	8	0	0	t
553	81	1	9	0	0	t
554	82	1	3	0	0	t
555	82	1	6	0	0	t
556	82	1	7	0	0	t
557	82	1	8	0	0	t
558	82	1	9	0	0	t
559	83	1	3	0	0	t
560	83	1	6	0	0	t
561	83	1	7	0	0	t
562	83	1	8	0	0	t
563	83	1	9	0	0	t
564	84	1	3	0	0	t
565	84	1	6	0	0	t
566	84	1	7	0	0	t
567	84	1	8	0	0	t
568	84	1	9	0	0	t
569	85	1	2	0	0	t
570	85	1	5	0	0	t
571	85	1	3	0	0	t
572	85	1	6	0	0	t
573	85	1	7	0	0	t
574	85	1	8	0	0	t
575	85	1	9	0	0	t
576	86	1	3	0	0	t
577	86	1	6	0	0	t
578	86	1	7	0	0	t
579	86	1	8	0	0	t
580	86	1	9	0	0	t
581	87	1	8	0	0	t
582	87	1	9	0	0	t
583	88	1	3	0	0	t
584	88	1	6	0	0	t
585	88	1	7	0	0	t
586	88	1	8	0	0	t
587	88	1	9	0	0	t
588	89	1	3	0	0	t
589	89	1	6	0	0	t
590	89	1	7	0	0	t
591	89	1	8	0	0	t
592	89	1	9	0	0	t
593	90	1	3	0	0	t
594	90	1	6	0	0	t
595	90	1	7	0	0	t
596	90	1	8	0	0	t
597	90	1	9	0	0	t
598	91	1	3	0	0	t
599	91	1	6	0	0	t
600	91	1	7	0	0	t
601	91	1	8	0	0	t
602	91	1	9	0	0	t
603	92	1	3	0	0	t
604	92	1	6	0	0	t
605	92	1	7	0	0	t
606	92	1	8	0	0	t
607	92	1	9	0	0	t
608	93	1	3	0	0	t
609	93	1	6	0	0	t
610	93	1	7	0	0	t
611	93	1	8	0	0	t
612	93	1	9	0	0	t
613	94	1	3	0	0	t
614	94	1	6	0	0	t
615	94	1	7	0	0	t
616	94	1	8	0	0	t
617	94	1	9	0	0	t
618	95	1	3	0	0	t
619	95	1	6	0	0	t
620	95	1	7	0	0	t
621	95	1	8	0	0	t
622	95	1	9	0	0	t
623	96	1	3	0	0	t
624	96	1	6	0	0	t
625	96	1	7	0	0	t
626	96	1	8	0	0	t
627	96	1	9	0	0	t
628	97	1	3	0	0	t
629	97	1	6	0	0	t
630	97	1	7	0	0	t
631	97	1	8	0	0	t
632	97	1	9	0	0	t
633	98	1	3	0	0	t
634	98	1	6	0	0	t
635	98	1	7	0	0	t
636	98	1	8	0	0	t
637	98	1	9	0	0	t
638	99	1	3	0	0	t
639	99	1	6	0	0	t
640	99	1	7	0	0	t
641	99	1	8	0	0	t
642	99	1	9	0	0	t
643	100	1	3	0	0	t
644	100	1	6	0	0	t
645	100	1	7	0	0	t
646	100	1	8	0	0	t
647	100	1	9	0	0	t
648	101	1	3	0	0	t
649	101	1	6	0	0	t
650	101	1	7	0	0	t
651	101	1	8	0	0	t
652	101	1	9	0	0	t
653	102	1	3	0	0	t
654	102	1	6	0	0	t
655	102	1	7	0	0	t
656	102	1	8	0	0	t
657	102	1	9	0	0	t
658	103	1	3	0	0	t
659	103	1	6	0	0	t
660	103	1	7	0	0	t
661	103	1	8	0	0	t
662	103	1	9	0	0	t
663	104	1	3	0	0	t
664	104	1	6	0	0	t
665	104	1	7	0	0	t
666	104	1	8	0	0	t
667	104	1	9	0	0	t
668	105	1	3	0	0	t
669	105	1	6	0	0	t
670	105	1	7	0	0	t
671	105	1	8	0	0	t
672	105	1	9	0	0	t
673	106	1	3	0	0	t
674	106	1	6	0	0	t
675	106	1	7	0	0	t
676	106	1	8	0	0	t
677	106	1	9	0	0	t
678	107	1	3	0	0	t
679	107	1	6	0	0	t
680	107	1	7	0	0	t
681	107	1	8	0	0	t
682	107	1	9	0	0	t
683	108	1	3	0	0	t
684	108	1	6	0	0	t
685	108	1	7	0	0	t
686	108	1	8	0	0	t
687	108	1	9	0	0	t
688	109	1	3	0	0	t
689	109	1	6	0	0	t
690	109	1	7	0	0	t
691	109	1	8	0	0	t
692	109	1	9	0	0	t
693	110	1	3	0	0	t
694	110	1	6	0	0	t
695	110	1	7	0	0	t
696	110	1	8	0	0	t
697	110	1	9	0	0	t
698	111	1	3	0	0	t
699	111	1	6	0	0	t
700	111	1	7	0	0	t
701	111	1	8	0	0	t
702	111	1	9	0	0	t
703	112	1	3	0	0	t
704	112	1	6	0	0	t
705	112	1	7	0	0	t
706	112	1	8	0	0	t
707	112	1	9	0	0	t
708	113	1	3	0	0	t
709	113	1	6	0	0	t
710	113	1	7	0	0	t
711	113	1	8	0	0	t
712	113	1	9	0	0	t
713	114	1	3	0	0	t
714	114	1	6	0	0	t
715	114	1	7	0	0	t
716	114	1	8	0	0	t
717	114	1	9	0	0	t
718	115	1	3	0	0	t
719	115	1	6	0	0	t
720	115	1	7	0	0	t
721	115	1	8	0	0	t
722	115	1	9	0	0	t
723	116	1	3	0	0	t
724	116	1	6	0	0	t
725	116	1	7	0	0	t
726	116	1	8	0	0	t
727	116	1	9	0	0	t
728	117	1	3	0	0	t
729	117	1	6	0	0	t
730	117	1	7	0	0	t
731	117	1	8	0	0	t
732	117	1	9	0	0	t
733	118	1	3	0	0	t
734	118	1	6	0	0	t
735	118	1	7	0	0	t
736	118	1	8	0	0	t
737	118	1	9	0	0	t
738	119	1	3	0	0	t
739	119	1	6	0	0	t
740	119	1	7	0	0	t
741	119	1	8	0	0	t
742	119	1	9	0	0	t
743	120	1	3	0	0	t
744	120	1	6	0	0	t
745	120	1	7	0	0	t
746	120	1	8	0	0	t
747	120	1	9	0	0	t
748	121	1	3	0	0	t
749	121	1	6	0	0	t
750	121	1	7	0	0	t
751	121	1	8	0	0	t
752	121	1	9	0	0	t
753	122	1	3	0	0	t
754	122	1	6	0	0	t
755	122	1	7	0	0	t
756	122	1	8	0	0	t
757	122	1	9	0	0	t
758	123	1	3	0	0	t
759	123	1	6	0	0	t
760	123	1	7	0	0	t
761	123	1	8	0	0	t
762	123	1	9	0	0	t
763	124	1	3	0	0	t
764	124	1	6	0	0	t
765	124	1	7	0	0	t
766	124	1	8	0	0	t
767	124	1	9	0	0	t
768	125	1	3	0	0	t
769	125	1	6	0	0	t
770	125	1	7	0	0	t
771	125	1	8	0	0	t
772	125	1	9	0	0	t
773	126	1	3	0	0	t
774	126	1	6	0	0	t
775	126	1	7	0	0	t
776	126	1	8	0	0	t
777	126	1	9	0	0	t
778	127	1	3	0	0	t
779	127	1	6	0	0	t
780	127	1	7	0	0	t
781	127	1	8	0	0	t
782	127	1	9	0	0	t
783	128	1	3	0	0	t
784	128	1	6	0	0	t
785	128	1	7	0	0	t
786	128	1	8	0	0	t
787	128	1	9	0	0	t
788	129	1	3	0	0	t
789	129	1	6	0	0	t
790	129	1	7	0	0	t
791	129	1	8	0	0	t
792	129	1	9	0	0	t
793	130	1	3	0	0	t
794	130	1	6	0	0	t
795	130	1	7	0	0	t
796	130	1	8	0	0	t
797	130	1	9	0	0	t
798	131	1	3	0	0	t
799	131	1	6	0	0	t
800	131	1	7	0	0	t
801	131	1	8	0	0	t
802	131	1	9	0	0	t
803	132	1	3	0	0	t
804	132	1	6	0	0	t
805	132	1	7	0	0	t
806	132	1	8	0	0	t
807	132	1	9	0	0	t
808	133	1	3	0	0	t
809	133	1	6	0	0	t
810	133	1	7	0	0	t
811	133	1	8	0	0	t
812	133	1	9	0	0	t
813	134	1	3	0	0	t
814	134	1	6	0	0	t
815	134	1	7	0	0	t
816	134	1	8	0	0	t
817	134	1	9	0	0	t
818	135	1	3	0	0	t
819	135	1	6	0	0	t
820	135	1	7	0	0	t
821	135	1	8	0	0	t
822	135	1	9	0	0	t
823	136	1	3	0	0	t
824	136	1	6	0	0	t
825	136	1	7	0	0	t
826	136	1	8	0	0	t
827	136	1	9	0	0	t
828	137	1	3	0	0	t
829	137	1	6	0	0	t
830	137	1	7	0	0	t
831	137	1	8	0	0	t
832	137	1	9	0	0	t
833	138	1	3	0	0	t
834	138	1	6	0	0	t
835	138	1	7	0	0	t
836	138	1	8	0	0	t
837	138	1	9	0	0	t
838	139	1	3	0	0	t
839	139	1	6	0	0	t
840	139	1	7	0	0	t
841	139	1	8	0	0	t
842	139	1	9	0	0	t
843	140	1	3	0	0	t
844	140	1	6	0	0	t
845	140	1	7	0	0	t
846	140	1	8	0	0	t
847	140	1	9	0	0	t
848	141	1	3	0	0	t
849	141	1	6	0	0	t
850	141	1	7	0	0	t
851	141	1	8	0	0	t
852	141	1	9	0	0	t
853	142	1	3	0	0	t
854	142	1	6	0	0	t
855	142	1	7	0	0	t
856	142	1	8	0	0	t
857	142	1	9	0	0	t
858	143	1	3	0	0	t
859	143	1	6	0	0	t
860	143	1	7	0	0	t
861	143	1	8	0	0	t
862	143	1	9	0	0	t
863	144	1	3	0	0	t
864	144	1	6	0	0	t
865	144	1	7	0	0	t
866	144	1	8	0	0	t
867	144	1	9	0	0	t
868	145	1	3	0	0	t
869	145	1	6	0	0	t
870	145	1	7	0	0	t
871	145	1	8	0	0	t
872	145	1	9	0	0	t
873	146	1	3	0	0	t
874	146	1	6	0	0	t
875	146	1	7	0	0	t
876	146	1	8	0	0	t
877	146	1	9	0	0	t
878	147	1	3	0	0	t
879	147	1	6	0	0	t
880	147	1	7	0	0	t
881	147	1	8	0	0	t
882	147	1	9	0	0	t
883	148	1	3	0	0	t
884	148	1	6	0	0	t
885	148	1	7	0	0	t
886	148	1	8	0	0	t
887	148	1	9	0	0	t
888	149	1	3	0	0	t
889	149	1	6	0	0	t
890	149	1	7	0	0	t
891	149	1	8	0	0	t
892	149	1	9	0	0	t
893	150	1	3	0	0	t
894	150	1	6	0	0	t
895	150	1	7	0	0	t
896	150	1	8	0	0	t
897	150	1	9	0	0	t
898	151	1	3	0	0	t
899	151	1	6	0	0	t
900	151	1	7	0	0	t
901	151	1	8	0	0	t
902	151	1	9	0	0	t
903	152	1	3	0	0	t
904	152	1	6	0	0	t
905	152	1	7	0	0	t
906	152	1	8	0	0	t
907	152	1	9	0	0	t
908	153	1	3	0	0	t
909	153	1	6	0	0	t
910	153	1	7	0	0	t
911	153	1	8	0	0	t
912	153	1	9	0	0	t
913	154	1	3	0	0	t
914	154	1	6	0	0	t
915	154	1	7	0	0	t
916	154	1	8	0	0	t
917	154	1	9	0	0	t
918	155	1	3	0	0	t
919	155	1	6	0	0	t
920	155	1	7	0	0	t
921	155	1	8	0	0	t
922	155	1	9	0	0	t
923	156	1	3	0	0	t
924	156	1	6	0	0	t
925	156	1	7	0	0	t
926	156	1	8	0	0	t
927	156	1	9	0	0	t
928	157	1	3	0	0	t
929	157	1	6	0	0	t
930	157	1	7	0	0	t
931	157	1	8	0	0	t
932	157	1	9	0	0	t
933	158	1	3	0	0	t
934	158	1	6	0	0	t
935	158	1	7	0	0	t
936	158	1	8	0	0	t
937	158	1	9	0	0	t
938	159	1	3	0	0	t
939	159	1	6	0	0	t
940	159	1	7	0	0	t
941	159	1	8	0	0	t
942	159	1	9	0	0	t
943	160	1	3	0	0	t
944	160	1	6	0	0	t
945	160	1	7	0	0	t
946	160	1	8	0	0	t
947	160	1	9	0	0	t
948	161	1	3	0	0	t
949	161	1	6	0	0	t
950	161	1	7	0	0	t
951	161	1	8	0	0	t
952	161	1	9	0	0	t
953	162	1	3	0	0	t
954	162	1	6	0	0	t
955	162	1	7	0	0	t
956	162	1	8	0	0	t
957	162	1	9	0	0	t
958	163	1	3	0	0	t
959	163	1	6	0	0	t
960	163	1	7	0	0	t
961	163	1	8	0	0	t
962	163	1	9	0	0	t
963	164	1	3	0	0	t
964	164	1	6	0	0	t
965	164	1	7	0	0	t
966	164	1	8	0	0	t
967	164	1	9	0	0	t
968	165	1	3	0	0	t
969	165	1	6	0	0	t
970	165	1	7	0	0	t
971	165	1	8	0	0	t
972	165	1	9	0	0	t
973	166	1	3	0	0	t
974	166	1	6	0	0	t
975	166	1	7	0	0	t
976	166	1	8	0	0	t
977	166	1	9	0	0	t
978	167	1	3	0	0	t
979	167	1	6	0	0	t
980	167	1	7	0	0	t
981	167	1	8	0	0	t
982	167	1	9	0	0	t
983	168	1	3	0	0	t
984	168	1	6	0	0	t
985	168	1	7	0	0	t
986	168	1	8	0	0	t
987	168	1	9	0	0	t
988	169	1	3	0	0	t
989	169	1	6	0	0	t
990	169	1	7	0	0	t
991	169	1	8	0	0	t
992	169	1	9	0	0	t
993	170	1	3	0	0	t
994	170	1	6	0	0	t
995	170	1	7	0	0	t
996	170	1	8	0	0	t
997	170	1	9	0	0	t
998	171	1	3	0	0	t
999	171	1	6	0	0	t
1000	171	1	7	0	0	t
1001	171	1	8	0	0	t
1002	171	1	9	0	0	t
1003	172	1	3	0	0	t
1004	172	1	6	0	0	t
1005	172	1	7	0	0	t
1006	172	1	8	0	0	t
1007	172	1	9	0	0	t
1008	173	1	3	0	0	t
1009	173	1	6	0	0	t
1010	173	1	7	0	0	t
1011	173	1	8	0	0	t
1012	173	1	9	0	0	t
1013	174	1	3	0	0	t
1014	174	1	6	0	0	t
1015	174	1	7	0	0	t
1016	174	1	8	0	0	t
1017	174	1	9	0	0	t
1018	175	1	3	0	0	t
1019	175	1	6	0	0	t
1020	175	1	7	0	0	t
1021	175	1	8	0	0	t
1022	175	1	9	0	0	t
1023	176	1	3	0	0	t
1024	176	1	6	0	0	t
1025	176	1	7	0	0	t
1026	176	1	8	0	0	t
1027	176	1	9	0	0	t
1028	177	1	3	0	0	t
1029	177	1	6	0	0	t
1030	177	1	7	0	0	t
1031	177	1	8	0	0	t
1032	177	1	9	0	0	t
1033	178	1	3	0	0	t
1034	178	1	6	0	0	t
1035	178	1	7	0	0	t
1036	178	1	8	0	0	t
1037	178	1	9	0	0	t
1038	179	1	3	0	0	t
1039	179	1	6	0	0	t
1040	179	1	7	0	0	t
1041	179	1	8	0	0	t
1042	179	1	9	0	0	t
1043	180	1	3	0	0	t
1044	180	1	6	0	0	t
1045	180	1	7	0	0	t
1046	180	1	8	0	0	t
1047	180	1	9	0	0	t
1048	181	1	3	0	0	t
1049	181	1	6	0	0	t
1050	181	1	7	0	0	t
1051	181	1	8	0	0	t
1052	181	1	9	0	0	t
1053	182	1	3	0	0	t
1054	182	1	6	0	0	t
1055	182	1	7	0	0	t
1056	182	1	8	0	0	t
1057	182	1	9	0	0	t
1058	183	1	3	0	0	t
1059	183	1	6	0	0	t
1060	183	1	7	0	0	t
1061	183	1	8	0	0	t
1062	183	1	9	0	0	t
1063	184	1	3	0	0	t
1064	184	1	6	0	0	t
1065	184	1	7	0	0	t
1066	184	1	8	0	0	t
1067	184	1	9	0	0	t
1068	185	1	3	0	0	t
1069	185	1	6	0	0	t
1070	185	1	7	0	0	t
1071	185	1	8	0	0	t
1072	185	1	9	0	0	t
1073	186	1	3	0	0	t
1074	186	1	6	0	0	t
1075	186	1	7	0	0	t
1076	186	1	8	0	0	t
1077	186	1	9	0	0	t
1078	187	1	3	0	0	t
1079	187	1	6	0	0	t
1080	187	1	7	0	0	t
1081	187	1	8	0	0	t
1082	187	1	9	0	0	t
1083	188	1	3	0	0	t
1084	188	1	6	0	0	t
1085	188	1	7	0	0	t
1086	188	1	8	0	0	t
1087	188	1	9	0	0	t
1088	189	1	3	0	0	t
1089	189	1	6	0	0	t
1090	189	1	7	0	0	t
1091	189	1	8	0	0	t
1092	189	1	9	0	0	t
1093	190	1	3	0	0	t
1094	190	1	6	0	0	t
1095	190	1	7	0	0	t
1096	190	1	8	0	0	t
1097	190	1	9	0	0	t
1098	191	1	3	0	0	t
1099	191	1	6	0	0	t
1100	191	1	7	0	0	t
1101	191	1	8	0	0	t
1102	191	1	9	0	0	t
1103	192	1	3	0	0	t
1104	192	1	6	0	0	t
1105	192	1	7	0	0	t
1106	192	1	8	0	0	t
1107	192	1	9	0	0	t
1108	193	1	3	0	0	t
1109	193	1	6	0	0	t
1110	193	1	7	0	0	t
1111	193	1	8	0	0	t
1112	193	1	9	0	0	t
1113	194	1	3	0	0	t
1114	194	1	6	0	0	t
1115	194	1	7	0	0	t
1116	194	1	8	0	0	t
1117	194	1	9	0	0	t
1118	195	1	3	0	0	t
1119	195	1	6	0	0	t
1120	195	1	7	0	0	t
1121	195	1	8	0	0	t
1122	195	1	9	0	0	t
1123	196	1	3	0	0	t
1124	196	1	6	0	0	t
1125	196	1	7	0	0	t
1126	196	1	8	0	0	t
1127	196	1	9	0	0	t
1128	197	1	3	0	0	t
1129	197	1	6	0	0	t
1130	197	1	7	0	0	t
1131	197	1	8	0	0	t
1132	197	1	9	0	0	t
1133	198	1	3	0	0	t
1134	198	1	6	0	0	t
1135	198	1	7	0	0	t
1136	198	1	8	0	0	t
1137	198	1	9	0	0	t
1138	199	1	3	0	0	t
1139	199	1	6	0	0	t
1140	199	1	7	0	0	t
1141	199	1	8	0	0	t
1142	199	1	9	0	0	t
1143	200	1	3	0	0	t
1144	200	1	6	0	0	t
1145	200	1	7	0	0	t
1146	200	1	8	0	0	t
1147	200	1	9	0	0	t
1148	201	1	3	0	0	t
1149	201	1	6	0	0	t
1150	201	1	7	0	0	t
1151	201	1	8	0	0	t
1152	201	1	9	0	0	t
1153	202	1	3	0	0	t
1154	202	1	6	0	0	t
1155	202	1	7	0	0	t
1156	202	1	8	0	0	t
1157	202	1	9	0	0	t
1158	203	1	3	0	0	t
1159	203	1	6	0	0	t
1160	203	1	7	0	0	t
1161	203	1	8	0	0	t
1162	203	1	9	0	0	t
1163	204	1	3	0	0	t
1164	204	1	6	0	0	t
1165	204	1	7	0	0	t
1166	204	1	8	0	0	t
1167	204	1	9	0	0	t
1168	205	1	3	0	0	t
1169	205	1	6	0	0	t
1170	205	1	7	0	0	t
1171	205	1	8	0	0	t
1172	205	1	9	0	0	t
1173	206	1	3	0	0	t
1174	206	1	6	0	0	t
1175	206	1	7	0	0	t
1176	206	1	8	0	0	t
1177	206	1	9	0	0	t
1178	207	1	3	0	0	t
1179	207	1	6	0	0	t
1180	207	1	7	0	0	t
1181	207	1	8	0	0	t
1182	207	1	9	0	0	t
1183	208	1	3	0	0	t
1184	208	1	6	0	0	t
1185	208	1	7	0	0	t
1186	208	1	8	0	0	t
1187	208	1	9	0	0	t
1188	209	1	2	0	0	t
1189	209	1	4	0	0	t
1190	209	1	5	0	0	t
1191	209	1	3	0	0	t
1192	209	1	6	0	0	t
1193	209	1	7	0	0	t
1194	209	1	8	0	0	t
1195	209	1	9	0	0	t
\.


--
-- Name: modelvalidations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modelvalidations_id_seq', 1195, true);


--
-- Data for Name: modelvariables; Type: TABLE DATA; Schema: public; Owner: -
--

COPY modelvariables (id, name, description, created, modified) FROM stdin;
1	nom_collectivite	Nom de la collectivité	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
2	adresse_collectivite	Adresse de la collectivité	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
3	cp_collectivite	Code postal de la collectivité	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
4	ville_collectivite	Ville de la collectivité	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
5	telephone_collectivite	Numéro de téléphone de la collectivité	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
6	date_jour_courant	Date du jour courant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
7	objet_projet	Libellé du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
8	titre_projet	Titre du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
9	position_projet	Position du projet dans l'ordre du jour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
10	identifiant_projet	Identifiant unique associé au projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
11	service_emetteur	Service émetteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
12	service_avec_hierarchie	Service émetteur avec hiérarchie	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
13	theme_projet	Thème du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
14	texte_projet	Texte de projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
15	note_synthese	Note de synthèse du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
16	texte_deliberation	Texte de délibération du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
17	etat_projet	-1 (refusé), 0 (en cours de rédaction), 1 (dans un circuit), 2 (validé), 3 (voté pour), 4 (voté contre), 5 (envoyé au ctrl de légalité)	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
18	salutation_rapporteur	Civilité du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
19	prenom_rapporteur	Prénom du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
20	nom_rapporteur	Nom du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
21	titre_rapporteur	Titre du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
22	email_rapporteur	Adresse mail du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
23	telmobile_rapporteur	Numéro de portable du rapporteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
24	telfixe_rapporteur	Numéro de téléphone fixe du rapporteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
25	date_naissance_rapporteur	Date de naissance du rapporteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
26	adresse1_rapporteur	Adresse 1 du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
27	adresse2_rapporteur	Adresse 2 du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
28	cp_rapporteur	Code postal du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
29	ville_rapporteur	ville du rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
30	note_rapporteur	Note rédigée sur le rapporteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
31	prenom_redacteur	Prénom du rédacteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
32	nom_redacteur	Nom du rédacteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
33	email_redacteur	Adresse mail du rédacteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
34	telmobile_redacteur	Numéro de téléphone du rédacteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
35	telfixe_redacteur	Numéro de téléphone du rédacteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
36	date_naissance_redacteur	Date de naissance du rédacteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
37	note_redacteur	Note rédigée sur le rédacteur du projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
38	identifiant_seance	Identifiant unique associé à la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
39	date_seance	Date de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
40	date_seance_lettres	Date de la séance en toutes lettres	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
41	heure_seance	Heure de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
42	hh_seance	Heure  /  minutes de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
43	mm_seance	Heure  /  minutes de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
44	type_seance	Type de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
45	nombre_seance	Nombre de séances auxquelles est inscrit un projet	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
46	texte_commentaire	Commentaire entré par un valideur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
47	Commentaires_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
48	avis	Avis attribué en commission (avec nom et date de la commission)	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
49	avis_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
50	commentaire	Commentaire saisi en commission	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
51	fichier	Permet d'afficher l'annexe ODT elle-même	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
52	nombre_annexe	Nombre d'annexes éditées	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
53	nom_fichier	Nom du fichier annexé	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
54	titre_annexe	Titre donné à l'annexe	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
55	nom_acteur	Nom de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
56	prenom_acteur	Prénom de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
57	salutation_acteur	Civilité de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
58	titre_acteur	Titre de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
59	date_naissance_acteur	Date de naissance de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
60	adresse1_acteur	Adresse 1 de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
61	adresse2_acteur	Adresse 2 de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
62	cp_acteur	Code postal de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
63	ville_acteur	Ville de résidence de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
64	email_acteur	Adresse mail de l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
65	telfixe_acteur	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
66	note_acteur	Note rédigée sur l'acteur convoqué	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
67	T1_theme	Permet une rupture par thème	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
68	T2_theme	Permet une rupture par thème	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
69	T3_theme	Permet une rupture par thème	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
70	debat_seance	Débats généraux de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
71	commentaire_seance	Commentaires de la séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
72	nom_secretaire	Nom du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
73	prenom_secretaire	Prénom du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
74	salutation_secretaire	Civilité du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
75	titre_secretaire	Titre du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
76	date_naissance_secretaire	Date de naissance du secrétaire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
77	adresse1_secretaire	Adresse 1 du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
78	adresse2_secretaire	Adresse 2 du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
79	cp_secretaire	Code postal du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
80	ville_secretaire	Ville de résidence du secrétaire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
81	email_secretaire	Adresse mail du secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
82	telfixe_secretaire	Numéro de téléphone fixe du secrétaire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
83	note_secretaire	Note rédigée sur le secrétaire de séance	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
84	numero_deliberation	Numéro attribué à la délibération	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
85	date_envoi_signature	date d'envoi à la signature	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
86	classification_deliberation	Classification matière choisie	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
87	date_reception	Date d'acquittement de la Préfecture	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
88	nombre_acteur_present	Nombre de présents	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
89	nombre_acteur_absent	Nombre d'absents	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
90	nombre_acteur_mandataire	Nombre de mandatés et mandataires	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
91	nombre_pour	Nombre d'acteurs ayant voté pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
92	nombre_abstention	Nombre d'acteurs s'étant abstenu	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
93	nombre_contre	Nombre d'acteurs ayant voté contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
94	nombre_sans_participation	Nombre d'acteurs ne participant pas au vote	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
95	nombre_votant	Nombre total de votants	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
96	commentaire_vote	Commentaires du vote	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
97	debat_deliberation	Débat de la délibération	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
98	nom_acteur_present	Nom de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
99	prenom_acteur_present	Prénom de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
100	salutation_acteur_present	Civilité de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
101	titre_acteur_present	Titre de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
102	date_naissance_acteur_present	Date de naissance de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
103	adresse1_acteur_present	Adresse 1 de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
104	adresse2_acteur_present	Adresse 2 de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
105	cp_acteur_present	Code postal de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
106	ville_acteur_present	Ville de résidence de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
107	email_acteur_present	Adresse mail de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
108	telfixe_acteur_present	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
109	telmobile_acteur_present	Numéro de portable de l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
110	note_acteur_present	Note rédigée sur l'acteur présent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
111	ActeursPresents_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
112	nom_acteur_absent	Nom de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
113	prenom_acteur_absent	Prénom de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
114	salutation_acteur_absent	Civilité de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
115	titre_acteur_absent	Titre de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
116	date_naissance_acteur_absent	Date de naissance de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
117	adresse1_acteur_absent	Adresse 1 de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
118	adresse2_acteur_absent	Adresse 2 de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
119	cp_acteur_absent	Code postal de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
120	ville_acteur_absent	Ville de résidence de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
121	email_acteur_absent	Adresse mail de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
122	telfixe_acteur_absent	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
123	telmobile_acteur_absent	Numéro de portable de l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
124	note_acteur_absent	Note rédigée sur l'acteur absent	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
125	ActeursAbsents_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
126	nom_acteur_mandataire	Nom de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
127	prenom_acteur_mandataire	Prénom de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
128	salutation_acteur_mandataire	Civilité de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
129	titre_acteur_mandataire	Titre de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
130	date_naissance_acteur_mandataire	Date de naissance de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
131	adresse1_acteur_mandataire	Adresse 1 de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
132	adresse2_acteur_mandataire	Adresse 2 de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
133	cp_acteur_mandataire	Code postal de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
134	ville_acteur_mandataire	Ville de résidence de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
135	email_acteur_mandataire	Adresse mail de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
136	telfixe_acteur_mandataire	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
137	telmobile_acteur_mandataire	Numéro de portable de l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
138	note_acteur_mandataire	Note rédigée sur l'acteur mandaté	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
139	nom_acteur_mandate	Nom de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
140	prenom_acteur_mandate	Prénom de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
141	salutation_acteur_mandate	Civilité de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
142	titre_acteur_mandate	Titre de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
143	date_naissance_acteur_mandate	Date de naissance de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
144	adresse1_acteur_mandate	Adresse 1 de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
145	adresse2_acteur_mandate	Adresse 2 de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
146	cp_acteur_mandate	Code postal de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
147	ville_acteur_mandate	Ville de résidence de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
148	email_acteur_mandate	Adresse mail de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
149	telfixe_acteur_mandate	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
150	telmobile_acteur_mandate	Numéro de portable de l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
151	note_acteur_mandate	Note rédigée sur l'acteur mandataire	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
152	ActeursMandates_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
153	nom_acteur_contre	Nom de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
154	prenom_acteur_contre	Prénom de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
155	salutation_acteur_contre	Civilité de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
156	titre_acteur_contre	Titre de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
157	date_naissance_acteur_contre	Date de naissance de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
158	adresse1_acteur_contre	Adresse 1 de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
159	adresse2_acteur_contre	Adresse 2 de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
160	cp_acteur_contre	Code postal de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
161	ville_acteur_contre	Ville de résidence de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
162	email_acteur_contre	Adresse mail de l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
163	telfixe_acteur_contre	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
164	telmobile_acteur_contre	Numéro de portable de l'acteur contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
165	note_acteur_contre	Note rédigée sur l'acteur votant contre	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
166	ActeursContre_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
167	nom_acteur_pour	Nom de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
168	prenom_acteur_pour	Prénom de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
169	salutation_acteur_pour	Civilité de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
170	titre_acteur_pour	Titre de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
171	date_naissance_acteur_pour	Date de naissance de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
172	adresse1_acteur_pour	Adresse 1 de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
173	adresse2_acteur_pour	Adresse 2 de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
174	cp_acteur_pour	Code postal de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
175	ville_acteur_pour	Ville de résidence de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
176	email_acteur_pour	Adresse mail de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
177	telfixe_acteur_pour	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
178	telmobile_acteur_pour	Numéro de portable de l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
179	note_acteur_pour	Note rédigée sur l'acteur votant pour	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
180	ActeursPour_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
181	nom_acteur_abstention	Nom de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
182	prenom_acteur_abstention	Prénom de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
183	salutation_acteur_abstention	Civilité de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
184	titre_acteur_abstention	Titre de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
185	date_naissance_acteur_abstention	Date de naissance de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
186	adresse1_acteur_abstention	Adresse 1 de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
187	adresse2_acteur_abstention	Adresse 2 de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
188	cp_acteur_abstention	Code postal de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
189	ville_acteur_abstention	Ville de résidence de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
190	email_acteur_abstention	Adresse mail de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
191	telfixe_acteur_abstention	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
192	telmobile_acteur_abstention	Numéro de portable de l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
193	note_acteur_abstention	Note rédigée sur l'acteur s'abstenant	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
194	ActeursAbstention_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
195	nom_acteur_sans_participation	Nom de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
196	prenom_acteur_sans_participation	Prénom de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
197	salutation_acteur_sans_participation	Civilité de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
198	titre_acteur_sans_participation	Titre de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
199	date_naissance_acteur_sans_participation	Date de naissance de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
200	adresse1_acteur_sans_participation	Adresse 1 de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
201	adresse2_acteur_sans_participation	Adresse 2 de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
202	cp_acteur_sans_participation	Code postal de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
203	ville_acteur_sans_participation	Ville de résidence de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
204	email_acteur_sans_participation	Adresse mail de l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
205	telfixe_acteur_sans_participation	Numéro de téléphone fixe de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
206	telmobile_acteur_sans_participation	Numéro de portable de l'acteur	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
207	note_acteur_sans_participation	Note rédigée sur l'acteur ne participant pas	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
208	acteurs_sans_participation_separator	Séparateur des itérations successives	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
209	texte_acte	Texte de l'acte	2014-02-12 16:31:56.13395	2014-02-12 16:31:56.13395
\.


--
-- Name: modelvariables_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modelvariables_id_seq', 1, false);


--
-- Data for Name: natures; Type: TABLE DATA; Schema: public; Owner: -
--

COPY natures (id, libelle, code, dua, sortfinal, communicabilite) FROM stdin;
1	Délibérations	DE	\N	\N	\N
2	Arrêtés Réglementaires	AR	\N	\N	\N
3	Arrêtés Individuels	AI	\N	\N	\N
4	Contrats et conventions	CC	\N	\N	\N
5	Autres	AU			
\.


--
-- Name: natures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('natures_id_seq', 1, false);


--
-- Data for Name: nomenclatures; Type: TABLE DATA; Schema: public; Owner: -
--

COPY nomenclatures (id, parent_id, libelle, lft, rght, created, modified) FROM stdin;
\.


--
-- Name: nomenclatures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nomenclatures_id_seq', 1, false);


--
-- Data for Name: profils; Type: TABLE DATA; Schema: public; Owner: -
--

COPY profils (id, parent_id, libelle, actif, created, modified) FROM stdin;
1	\N	Administrateur	t	2012-11-16 14:55:13	2012-11-16 14:55:13
\.


--
-- Name: profils_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('profils_id_seq', 2, true);


--
-- Data for Name: seances; Type: TABLE DATA; Schema: public; Owner: -
--

COPY seances (id, type_id, created, modified, date_convocation, date, traitee, commentaire, secretaire_id, president_id, debat_global, debat_global_name, debat_global_size, debat_global_type, pv_figes, pv_sommaire, pv_complet, numero_depot) FROM stdin;
\.


--
-- Name: seances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seances_id_seq', 1, false);


--
-- Data for Name: sequences; Type: TABLE DATA; Schema: public; Owner: -
--

COPY sequences (id, nom, commentaire, num_sequence, created, modified) FROM stdin;
\.


--
-- Name: sequences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('sequences_id_seq', 1, true);


--
-- Data for Name: services; Type: TABLE DATA; Schema: public; Owner: -
--

COPY services (id, parent_id, "order", libelle, circuit_defaut_id, actif, created, modified, lft, rght) FROM stdin;
1	0		Informatique	0	t	2012-11-16 14:54:44	2012-11-16 14:54:44	1	2
\.


--
-- Name: services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('services_id_seq', 1, true);


--
-- Data for Name: tdt_messages; Type: TABLE DATA; Schema: public; Owner: -
--

COPY tdt_messages (id, delib_id, message_id, type_message, type_reponse, created, modified, date_message, data) FROM stdin;
\.


--
-- Name: tdt_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('tdt_messages_id_seq', 1, false);


--
-- Data for Name: themes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY themes (id, parent_id, "order", libelle, actif, created, modified, lft, rght) FROM stdin;
1	0		Défaut	t	2012-11-16 14:54:57	2012-11-16 14:54:57	1	2
\.


--
-- Name: themes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('themes_id_seq', 1, true);


--
-- Data for Name: traitements; Type: TABLE DATA; Schema: public; Owner: -
--

COPY traitements (id, delib_id, circuit_id, "position", date_traitement) FROM stdin;
\.


--
-- Name: traitements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('traitements_id_seq', 1, false);


--
-- Data for Name: typeactes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY typeactes (id, libelle, modeleprojet_id, modelefinal_id, nature_id, compteur_id, created, modified, gabarit_projet, gabarit_synthese, gabarit_acte, teletransmettre) FROM stdin;
2	Délibération	1	1	1	1	2012-11-16	2012-11-16	\N	\N	\N	t
\.


--
-- Name: typeactes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeactes_id_seq', 2, true);


--
-- Data for Name: typeacteurs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY typeacteurs (id, nom, commentaire, elu, created, modified) FROM stdin;
\.


--
-- Name: typeacteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeacteurs_id_seq', 1, false);


--
-- Data for Name: typeseances; Type: TABLE DATA; Schema: public; Owner: -
--

COPY typeseances (id, libelle, retard, action, compteur_id, modelprojet_id, modeldeliberation_id, modelconvocation_id, modelordredujour_id, modelpvsommaire_id, modelpvdetaille_id, created, modified) FROM stdin;
\.


--
-- Data for Name: typeseances_acteurs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY typeseances_acteurs (id, typeseance_id, acteur_id) FROM stdin;
\.


--
-- Name: typeseances_acteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_acteurs_id_seq', 1, false);


--
-- Name: typeseances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_id_seq', 1, false);


--
-- Name: typeseances_natures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_natures_id_seq', 1, false);


--
-- Data for Name: typeseances_typeactes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY typeseances_typeactes (id, typeseance_id, typeacte_id) FROM stdin;
\.


--
-- Data for Name: typeseances_typeacteurs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY typeseances_typeacteurs (id, typeseance_id, typeacteur_id) FROM stdin;
\.


--
-- Name: typeseances_typeacteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_typeacteurs_id_seq', 1, false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY users (id, profil_id, statut, login, note, circuit_defaut_id, password, nom, prenom, email, telfixe, telmobile, date_naissance, accept_notif, mail_refus, mail_traitement, mail_insertion, "position", created, modified, mail_modif_projet_cree, mail_modif_projet_valide, mail_retard_validation) FROM stdin;
1	1	0	admin		\N	21232f297a57a5a743894a0e4a801fc3	Administrateur	Franck				\N	f	f	f	f	\N	2012-11-16 14:57:03	2014-02-12 16:33:11	f	f	f
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_id_seq', 1, true);


--
-- Data for Name: users_services; Type: TABLE DATA; Schema: public; Owner: -
--

COPY users_services (id, user_id, service_id) FROM stdin;
1	0	1
\.


--
-- Name: users_services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_services_id_seq', 1, true);


--
-- Data for Name: votes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY votes (id, acteur_id, delib_id, resultat, created, modified) FROM stdin;
\.


--
-- Name: votes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('votes_id_seq', 1, false);


--
-- Data for Name: wkf_circuits; Type: TABLE DATA; Schema: public; Owner: -
--

COPY wkf_circuits (id, nom, description, actif, defaut, created_user_id, modified_user_id, created, modified) FROM stdin;
\.


--
-- Name: wkf_circuits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_circuits_id_seq', 1, false);


--
-- Data for Name: wkf_compositions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY wkf_compositions (id, etape_id, type_validation, soustype, type_composition, trigger_id, created_user_id, modified_user_id, created, modified) FROM stdin;
\.


--
-- Name: wkf_compositions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_compositions_id_seq', 1, false);


--
-- Data for Name: wkf_etapes; Type: TABLE DATA; Schema: public; Owner: -
--

COPY wkf_etapes (id, circuit_id, nom, description, type, soustype, ordre, created_user_id, modified_user_id, created, modified, cpt_retard) FROM stdin;
\.


--
-- Name: wkf_etapes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_etapes_id_seq', 1, false);


--
-- Data for Name: wkf_signatures; Type: TABLE DATA; Schema: public; Owner: -
--

COPY wkf_signatures (id, type_signature, signature, visa_id) FROM stdin;
\.


--
-- Name: wkf_signatures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_signatures_id_seq', 1, false);


--
-- Data for Name: wkf_traitements; Type: TABLE DATA; Schema: public; Owner: -
--

COPY wkf_traitements (id, circuit_id, target_id, numero_traitement, treated_orig, created_user_id, modified_user_id, created, modified, treated) FROM stdin;
\.


--
-- Name: wkf_traitements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_traitements_id_seq', 1, false);


--
-- Data for Name: wkf_visas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY wkf_visas (id, traitement_id, trigger_id, signature_id, etape_nom, etape_type, action, commentaire, date, numero_traitement, type_validation, etape_id, date_retard) FROM stdin;
\.


--
-- Name: wkf_visas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_visas_id_seq', 1, false);


--
-- Name: acos_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acos
    ADD CONSTRAINT acos_pkey PRIMARY KEY (id);


--
-- Name: acteurs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acteurs
    ADD CONSTRAINT acteurs_pkey PRIMARY KEY (id);


--
-- Name: acteurs_seances_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acteurs_seances
    ADD CONSTRAINT acteurs_seances_pkey PRIMARY KEY (id);


--
-- Name: acteurs_services_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY acteurs_services
    ADD CONSTRAINT acteurs_services_pkey PRIMARY KEY (id);


--
-- Name: ados_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ados
    ADD CONSTRAINT ados_pkey PRIMARY KEY (id);


--
-- Name: annexes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY annexes
    ADD CONSTRAINT annexes_pkey PRIMARY KEY (id);


--
-- Name: aros_acos_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aros_acos
    ADD CONSTRAINT aros_acos_pkey PRIMARY KEY (id);


--
-- Name: aros_ados_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aros_ados
    ADD CONSTRAINT aros_ados_pkey PRIMARY KEY (id);


--
-- Name: aros_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY aros
    ADD CONSTRAINT aros_pkey PRIMARY KEY (id);


--
-- Name: circuits_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY circuits_users
    ADD CONSTRAINT circuits_users_pkey PRIMARY KEY (id);


--
-- Name: collectivites_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY collectivites
    ADD CONSTRAINT collectivites_pkey PRIMARY KEY (id);


--
-- Name: commentaires_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY commentaires
    ADD CONSTRAINT commentaires_pkey PRIMARY KEY (id);


--
-- Name: compteurs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY compteurs
  ADD CONSTRAINT compteurs_pkey PRIMARY KEY (id);


--
-- Name: crons_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY crons
  ADD CONSTRAINT crons_pkey PRIMARY KEY (id);


--
-- Name: deliberations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY deliberations
    ADD CONSTRAINT deliberations_pkey PRIMARY KEY (id);


--
-- Name: deliberations_seances_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY deliberations_seances
    ADD CONSTRAINT deliberations_seances_pkey PRIMARY KEY (id);


--
-- Name: deliberations_typeseances_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY deliberations_typeseances
    ADD CONSTRAINT deliberations_typeseances_pkey PRIMARY KEY (id);


--
-- Name: historiques_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY historiques
    ADD CONSTRAINT historiques_pkey PRIMARY KEY (id);


--
-- Name: infosupdefs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY infosupdefs
    ADD CONSTRAINT infosupdefs_pkey PRIMARY KEY (id);


--
-- Name: infosuplistedefs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY infosuplistedefs
    ADD CONSTRAINT infosuplistedefs_pkey PRIMARY KEY (id);


--
-- Name: infosups_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY infosups
    ADD CONSTRAINT infosups_pkey PRIMARY KEY (id);


--
-- Name: listepresences_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY listepresences
    ADD CONSTRAINT listepresences_pkey PRIMARY KEY (id);


--
-- Name: models_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modeltemplates
    ADD CONSTRAINT models_pkey PRIMARY KEY (id);


--
-- Name: modelsections_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modelsections
    ADD CONSTRAINT modelsections_pkey PRIMARY KEY (id);


--
-- Name: modeltypes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modeltypes
    ADD CONSTRAINT modeltypes_pkey PRIMARY KEY (id);


--
-- Name: modelvalidations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modelvalidations
    ADD CONSTRAINT modelvalidations_pkey PRIMARY KEY (id);


--
-- Name: modelvariables_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modelvariables
    ADD CONSTRAINT modelvariables_pkey PRIMARY KEY (id);


--
-- Name: natures_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY natures
    ADD CONSTRAINT natures_pkey PRIMARY KEY (id);


--
-- Name: nomenclatures_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY nomenclatures
    ADD CONSTRAINT nomenclatures_pkey PRIMARY KEY (id);


--
-- Name: profils_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY profils
    ADD CONSTRAINT profils_pkey PRIMARY KEY (id);


--
-- Name: seances_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY seances
    ADD CONSTRAINT seances_pkey PRIMARY KEY (id);


--
-- Name: sequences_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sequences
    ADD CONSTRAINT sequences_pkey PRIMARY KEY (id);


--
-- Name: services_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- Name: tdt_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tdt_messages
    ADD CONSTRAINT tdt_messages_pkey PRIMARY KEY (id);


--
-- Name: themes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY themes
    ADD CONSTRAINT themes_pkey PRIMARY KEY (id);


--
-- Name: traitements_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY traitements
    ADD CONSTRAINT traitements_pkey PRIMARY KEY (id);


--
-- Name: typeactes_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY typeactes
    ADD CONSTRAINT typeactes_id_key UNIQUE (id);


--
-- Name: typeacteurs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY typeacteurs
    ADD CONSTRAINT typeacteurs_pkey PRIMARY KEY (id);


--
-- Name: typeseances_acteurs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY typeseances_acteurs
    ADD CONSTRAINT typeseances_acteurs_pkey PRIMARY KEY (id);


--
-- Name: typeseances_natures_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY typeseances_typeactes
    ADD CONSTRAINT typeseances_natures_pkey PRIMARY KEY (id);


--
-- Name: typeseances_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY typeseances
    ADD CONSTRAINT typeseances_pkey PRIMARY KEY (id);


--
-- Name: typeseances_typeacteurs_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY typeseances_typeacteurs
    ADD CONSTRAINT typeseances_typeacteurs_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users_services_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users_services
    ADD CONSTRAINT users_services_pkey PRIMARY KEY (id);


--
-- Name: votes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY votes
    ADD CONSTRAINT votes_pkey PRIMARY KEY (id);


--
-- Name: wkf_circuits_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY wkf_circuits
    ADD CONSTRAINT wkf_circuits_pkey PRIMARY KEY (id);


--
-- Name: wkf_compositions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY wkf_compositions
    ADD CONSTRAINT wkf_compositions_pkey PRIMARY KEY (id);


--
-- Name: wkf_etapes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY wkf_etapes
    ADD CONSTRAINT wkf_etapes_pkey PRIMARY KEY (id);


--
-- Name: wkf_signatures_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY wkf_signatures
    ADD CONSTRAINT wkf_signatures_pkey PRIMARY KEY (id);


--
-- Name: wkf_traitements_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY wkf_traitements
    ADD CONSTRAINT wkf_traitements_pkey PRIMARY KEY (id);


--
-- Name: wkf_visas_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY wkf_visas
    ADD CONSTRAINT wkf_visas_pkey PRIMARY KEY (id);


--
-- Name: INFOSUPDEF_ID_ORDRE; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX "INFOSUPDEF_ID_ORDRE" ON infosuplistedefs USING btree (infosupdef_id, ordre);


--
-- Name: aco_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX aco_id ON aros_acos USING btree (aco_id);


--
-- Name: acos_idx1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acos_idx1 ON acos USING btree (lft, rght);


--
-- Name: acos_idx2; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acos_idx2 ON acos USING btree (alias);


--
-- Name: acos_idx3; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acos_idx3 ON acos USING btree (model, foreign_key);


--
-- Name: acos_leftright; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acos_leftright ON acos USING btree (lft, rght);


--
-- Name: acteur_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acteur_id ON votes USING btree (acteur_id);


--
-- Name: acteurs_actif; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acteurs_actif ON acteurs USING btree (actif);


--
-- Name: acteursservices_acteur_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acteursservices_acteur_id ON acteurs_services USING btree (acteur_id);


--
-- Name: acteursservices_service_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX acteursservices_service_id ON acteurs_services USING btree (service_id);


--
-- Name: ado_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX ado_id ON aros_ados USING btree (ado_id);


--
-- Name: alias; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX alias ON aros USING btree (alias);


--
-- Name: alias_2; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX alias_2 ON aros USING btree (alias);


--
-- Name: annexes_joindre; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX annexes_joindre ON annexes USING btree (foreign_key, joindre_fusion);


--
-- Name: aro_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX aro_id ON aros_ados USING btree (aro_id);


--
-- Name: aros_idx1; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX aros_idx1 ON aros USING btree (lft, rght);


--
-- Name: aros_idx2; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX aros_idx2 ON aros USING btree (alias);


--
-- Name: aros_idx3; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX aros_idx3 ON aros USING btree (model, foreign_key);


--
-- Name: aros_leftright; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX aros_leftright ON aros USING btree (lft, rght);


--
-- Name: circuit_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX circuit_id ON wkf_etapes USING btree (circuit_id);


--
-- Name: circuits; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX circuits ON wkf_traitements USING btree (circuit_id);


--
-- Name: created_user_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX created_user_id ON wkf_circuits USING btree (created_user_id);


--
-- Name: deliberation_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX deliberation_id ON votes USING btree (delib_id);


--
-- Name: deliberations_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX deliberations_id ON deliberations_seances USING btree (deliberation_id);


--
-- Name: deliberations_seances_seance; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX deliberations_seances_seance ON deliberations_seances USING btree (seance_id);


--
-- Name: elu; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX elu ON typeacteurs USING btree (elu);


--
-- Name: etape_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX etape_id ON wkf_compositions USING btree (etape_id);


--
-- Name: etat; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX etat ON deliberations USING btree (etat);


--
-- Name: foreign_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX foreign_key ON infosups USING btree (foreign_key);


--
-- Name: index; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX index ON acteurs USING btree (id);


--
-- Name: infosupdef_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX infosupdef_id ON infosups USING btree (infosupdef_id);


--
-- Name: lft; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX lft ON acos USING btree (lft);


--
-- Name: login; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX login ON users USING btree (login);


--
-- Name: model; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX model ON infosupdefs USING btree (model);


--
-- Name: model_foreign_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX model_foreign_key ON annexes USING btree (model, foreign_key);


--
-- Name: modified_user_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX modified_user_id ON wkf_circuits USING btree (modified_user_id);


--
-- Name: nature_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX nature_id ON deliberations USING btree (typeacte_id);


--
-- Name: nom; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX nom ON wkf_etapes USING btree (nom);


--
-- Name: parent; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX parent ON deliberations USING btree (parent_id);


--
-- Name: parent_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX parent_id ON aros USING btree (parent_id);


--
-- Name: rapporteur_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX rapporteur_id ON deliberations USING btree (rapporteur_id);


--
-- Name: redacteur_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX redacteur_id ON deliberations USING btree (redacteur_id);


--
-- Name: rght; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX rght ON aros USING btree (rght);


--
-- Name: seances_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX seances_id ON deliberations_seances USING btree (seance_id);


--
-- Name: seances_traitee; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX seances_traitee ON seances USING btree (traitee);


--
-- Name: service_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX service_id ON deliberations USING btree (service_id);


--
-- Name: suppleant_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX suppleant_id ON acteurs USING btree (suppleant_id);


--
-- Name: target; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX target ON wkf_traitements USING btree (target_id);


--
-- Name: tdtmsg_; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX tdtmsg_ ON tdt_messages USING btree (delib_id);


--
-- Name: text; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX text ON infosups USING btree (text);


--
-- Name: theme_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX theme_id ON deliberations USING btree (theme_id);


--
-- Name: themes_left; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX themes_left ON themes USING btree (lft);


--
-- Name: traitements_treated; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX traitements_treated ON wkf_traitements USING btree (treated_orig);


--
-- Name: trigger; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX trigger ON wkf_compositions USING btree (trigger_id);


--
-- Name: type_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX type_id ON seances USING btree (type_id);


--
-- Name: typeacteur; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX typeacteur ON acteurs USING btree (typeacteur_id);


--
-- Name: typeseance_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX typeseance_id ON typeseances_typeacteurs USING btree (typeseance_id, typeacteur_id);


--
-- Name: typeseancenature_nature; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX typeseancenature_nature ON typeseances_typeactes USING btree (typeacte_id);


--
-- Name: user; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX "user" ON historiques USING btree (user_id);


--
-- Name: user_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX user_id ON circuits_users USING btree (user_id);


--
-- Name: users_services_users; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX users_services_users ON users_services USING btree (user_id);


--
-- Name: wkf_visas_traitements; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX wkf_visas_traitements ON wkf_visas USING btree (traitement_id);


--
-- Name: modelsections_parent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelsections
    ADD CONSTRAINT modelsections_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES modelsections(id);


--
-- Name: modeltemplates_modeltype_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY modeltemplates
    ADD CONSTRAINT modeltemplates_modeltype_id_fkey FOREIGN KEY (modeltype_id) REFERENCES modeltypes(id);


--
-- Name: modelvalidations_modelsection_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelvalidations
    ADD CONSTRAINT modelvalidations_modelsection_id_fkey FOREIGN KEY (modelsection_id) REFERENCES modelsections(id);


--
-- Name: modelvalidations_modeltype_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelvalidations
    ADD CONSTRAINT modelvalidations_modeltype_id_fkey FOREIGN KEY (modeltype_id) REFERENCES modeltypes(id);


--
-- Name: modelvalidations_modelvariable_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY modelvalidations
    ADD CONSTRAINT modelvalidations_modelvariable_id_fkey FOREIGN KEY (modelvariable_id) REFERENCES modelvariables(id);


--
-- Name: wkf_signatures_visa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY wkf_signatures
    ADD CONSTRAINT wkf_signatures_visa_id_fkey FOREIGN KEY (visa_id) REFERENCES wkf_visas(id);


--
-- Name: wkf_visas_etape_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY wkf_visas
    ADD CONSTRAINT wkf_visas_etape_id_fkey FOREIGN KEY (etape_id) REFERENCES wkf_etapes(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

