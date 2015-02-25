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

--CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

--COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


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
-- Name: crons_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE crons_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

--
-- Name: crons; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE crons (
    id integer DEFAULT nextval('crons_id_seq'::regclass) NOT NULL,
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
    lock boolean DEFAULT false NOT NULL
);

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
    signature bytea,
    signee boolean default false NOT NULL,
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
    circuit_id integer,
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
    numero_depot integer DEFAULT 0 NOT NULL,
    idelibre_id character varying
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
    teletransmettre boolean DEFAULT true,
    gabarit_acte_name character varying,
    gabarit_projet_name character varying,
    gabarit_synthese_name character varying
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
    mail_retard_validation boolean DEFAULT false,
    theme character varying
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

INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (73, 'Pages:home', 1, 2, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (75, 'Deliberations:add', 4, 5, 74, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (76, 'Deliberations:mesProjetsRedaction', 6, 7, 74, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (77, 'Deliberations:mesProjetsValidation', 8, 9, 74, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (78, 'Deliberations:mesProjetsATraiter', 10, 11, 74, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (79, 'Deliberations:mesProjetsValides', 12, 13, 74, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (74, 'Pages:mes_projets', 3, 16, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (80, 'Deliberations:mesProjetsRecherche', 14, 15, 74, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (81, 'Pages:projets_mon_service', 17, 20, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (82, 'Deliberations:projetsMonService', 18, 19, 81, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (84, 'Deliberations:tousLesProjetsSansSeance', 22, 23, 83, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (85, 'Deliberations:tousLesProjetsValidation', 24, 25, 83, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (86, 'Deliberations:tousLesProjetsAFaireVoter', 26, 27, 83, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (83, 'Pages:tous_les_projets', 21, 30, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (87, 'Deliberations:tousLesProjetsRecherche', 28, 29, 83, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (89, 'Deliberations:autresActesAValider', 32, 33, 88, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (90, 'Deliberations:autreActesValides', 34, 35, 88, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (91, 'Deliberations:autreActesAEnvoyer', 36, 37, 88, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (92, 'Deliberations:autreActesEnvoyes', 38, 39, 88, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (88, 'Pages:autresActesAValider', 31, 42, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (93, 'Deliberations:nonTransmis', 40, 41, 88, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (95, 'Seances:add', 44, 45, 94, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (96, 'Seances:listerFuturesSeances', 46, 47, 94, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (97, 'Seances:listerAnciennesSeances', 48, 49, 94, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (94, 'Pages:listerFuturesSeances', 43, 52, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (98, 'Seances:afficherCalendrier', 50, 51, 94, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (100, 'Postseances:index', 54, 55, 99, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (101, 'Deliberations:sendToParapheur', 56, 57, 99, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (102, 'Deliberations:toSend', 58, 59, 99, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (103, 'Deliberations:transmit', 60, 61, 99, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (99, 'Pages:postseances', 53, 64, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (104, 'Deliberations:verserAsalae', 62, 63, 99, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (106, 'Profils:index', 66, 67, 105, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (107, 'Services:index', 68, 69, 105, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (108, 'Users:index', 70, 71, 105, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (105, 'Pages:gestion_utilisateurs', 65, 74, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (109, 'Circuits:index', 72, 73, 105, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (111, 'Typeacteurs:index', 76, 77, 110, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (110, 'Pages:gestion_acteurs', 75, 80, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (112, 'Acteurs:index', 78, 79, 110, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (114, 'Collectivites:index', 82, 83, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (115, 'Themes:index', 84, 85, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (116, 'Model_odt_validator:modeltemplates', 86, 87, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (117, 'Sequences:index', 88, 89, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (118, 'Compteurs:index', 90, 91, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (119, 'Infosupdefs:index', 92, 93, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (120, 'Infosupdefs:index_seance', 94, 95, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (121, 'Typeactes:index', 96, 97, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (113, 'Pages:administration', 81, 100, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (122, 'Typeseances:index', 98, 99, 113, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (124, 'Connecteurs:index', 102, 103, 123, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (125, 'Crons:index', 104, 105, 123, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (137, 'Deliberations:editerTous', 120, 121, 130, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (123, 'Pages:connecteurs', 101, 108, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (131, 'Deliberations:edit', 110, 111, 130, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (132, 'Deliberations:delete', 112, 113, 130, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (133, 'Deliberations:goNext', 114, 115, 130, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (134, 'Deliberations:validerEnUrgence', 116, 117, 130, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (135, 'Deliberations:rebond', 118, 119, 130, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (138, 'Modelvalidations:index', 106, 107, 123, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (130, 'Module:Deliberations', 109, 122, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (139, 'Module:CakeflowApp', 123, 134, 0, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (144, 'CakeflowApp:formatLinkedModels', 132, 133, 139, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (140, 'CakeflowApp:setCreatedModifiedUser', 124, 125, 139, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (141, 'CakeflowApp:formatUser', 126, 127, 139, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (142, 'CakeflowApp:formatLinkedModel', 128, 129, 139, NULL, 0);
INSERT INTO acos (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (143, 'CakeflowApp:listLinkedModel', 130, 131, 139, NULL, 0);


--
-- Name: acos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acos_id_seq', 145, true);


--
-- Data for Name: acteurs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: acteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acteurs_id_seq', 1, true);


--
-- Data for Name: acteurs_seances; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: acteurs_seances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acteurs_seances_id_seq', 1, true);


--
-- Data for Name: acteurs_services; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: acteurs_services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('acteurs_services_id_seq', 1, false);


--
-- Data for Name: ados; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO ados (id, alias, lft, rght, parent_id, model, foreign_key) VALUES (1, 'Typeacte:Délibération', NULL, NULL, 0, 'Typeacte', 1);


--
-- Name: ados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('ados_id_seq', 2, true);


--
-- Data for Name: annexes; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: annexes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('annexes_id_seq', 1, true);


--
-- Data for Name: aros; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO aros (id, foreign_key, alias, lft, rght, parent_id, model) VALUES (1, 1, 'Administrateur', 1, 4, NULL, 'Profil');
INSERT INTO aros (id, foreign_key, alias, lft, rght, parent_id, model) VALUES (2, 1, 'admin', 2, 3, 1, 'User');


--
-- Data for Name: aros_acos; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (139, 1, 73, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (140, 1, 74, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (141, 1, 75, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (142, 1, 76, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (143, 1, 77, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (144, 1, 78, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (146, 1, 80, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (147, 1, 81, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (148, 1, 82, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (149, 1, 83, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (150, 1, 84, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (151, 1, 85, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (152, 1, 86, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (153, 1, 87, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (154, 1, 88, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (155, 1, 89, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (156, 1, 90, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (157, 1, 91, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (158, 1, 92, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (159, 1, 93, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (160, 1, 94, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (161, 1, 95, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (163, 1, 97, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (164, 1, 98, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (165, 1, 99, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (166, 1, 100, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (167, 1, 101, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (168, 1, 102, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (169, 1, 103, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (170, 1, 104, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (171, 1, 105, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (172, 1, 106, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (173, 1, 107, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (174, 1, 108, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (175, 1, 109, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (176, 1, 110, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (177, 1, 111, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (178, 1, 112, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (180, 1, 114, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (181, 1, 115, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (182, 1, 116, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (183, 1, 117, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (184, 1, 118, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (185, 1, 119, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (186, 1, 120, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (187, 1, 121, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (188, 1, 122, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (189, 1, 123, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (190, 1, 124, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (191, 1, 125, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (192, 1, 138, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (193, 1, 130, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (194, 1, 131, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (195, 1, 132, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (197, 1, 134, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (198, 1, 135, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (200, 1, 137, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (700, 2, 100, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (701, 2, 101, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (702, 2, 102, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (703, 2, 103, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (704, 2, 104, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (705, 2, 105, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (706, 2, 106, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (707, 2, 107, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (708, 2, 108, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (709, 2, 109, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (710, 2, 110, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (711, 2, 111, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (712, 2, 112, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (713, 2, 113, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (714, 2, 114, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (715, 2, 115, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (716, 2, 116, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (717, 2, 117, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (718, 2, 118, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (719, 2, 119, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (720, 2, 120, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (721, 2, 121, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (722, 2, 122, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (723, 2, 123, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (724, 2, 124, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (725, 2, 125, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (726, 2, 138, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (727, 2, 130, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (728, 2, 131, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (729, 2, 132, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (730, 2, 133, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (731, 2, 134, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (732, 2, 135, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (733, 2, 137, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (145, 1, 79, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (162, 1, 96, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (179, 1, 113, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (196, 1, 133, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (673, 2, 73, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (674, 2, 74, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (675, 2, 75, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (676, 2, 76, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (677, 2, 77, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (678, 2, 78, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (679, 2, 79, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (680, 2, 80, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (681, 2, 81, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (682, 2, 82, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (683, 2, 83, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (684, 2, 84, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (685, 2, 85, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (686, 2, 86, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (687, 2, 87, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (688, 2, 88, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (689, 2, 89, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (690, 2, 90, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (691, 2, 91, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (692, 2, 92, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (693, 2, 93, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (694, 2, 94, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (695, 2, 95, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (696, 2, 96, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (697, 2, 97, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (698, 2, 98, '1 ', '1 ', '1 ', '1 ');
INSERT INTO aros_acos (id, aro_id, aco_id, _create, _read, _update, _delete) VALUES (699, 2, 99, '1 ', '1 ', '1 ', '1 ');


--
-- Name: aros_acos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('aros_acos_id_seq', 733, true);


--
-- Data for Name: aros_ados; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO aros_ados (id, aro_id, ado_id, _create, _read, _update, _delete) VALUES (1, 2, 1, '1 ', '1 ', '1 ', '1 ');


--
-- Name: aros_ados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('aros_ados_id_seq', 2, true);


--
-- Name: aros_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('aros_id_seq', 3, true);


--
-- Data for Name: circuits_users; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: circuits_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('circuits_users_id_seq', 1, true);


--
-- Data for Name: collectivites; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO collectivites (id, id_entity, nom, adresse, "CP", ville, telephone, logo) VALUES (1, 1, 'ADULLACT', '836, rue du Mas de Verchant', 34000, 'Montpellier', '04 67 65 05 88', NULL);


--
-- Data for Name: commentaires; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: commentaires_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('commentaires_id_seq', 1, true);


--
-- Data for Name: compteurs; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO compteurs (id, nom, commentaire, def_compteur, sequence_id, def_reinit, val_reinit, created, modified) VALUES (1, 'Arrêtés', '', '#AAAA#_#000#', 1, '#AAAA#', NULL, '2014-03-14 16:34:10', '2014-03-14 16:34:10');


--
-- Name: compteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('compteurs_id_seq', 2, true);


--
-- Data for Name: crons; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (7, 'Génération de document : conversion des annexes', 'Conversion planifiée des annexes vers les formats odt et pdf pour la génération', NULL, 'convertionAnnexesJob', false, NULL, NULL, 'P1D', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 1, 'CronJob', false);
INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (2, 'Circuits de traitement : Déclenchement des alertes de retard', 'Relance les utilisateurs qui sont en retard dans la validation d''un projet dans leur bannette', NULL, 'retardCakeflowJob', false, NULL, NULL, 'PT1H', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 1, 'CronJob', false);
INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (4, 'TDT : Mise à jours des mails sécurisés', 'Envoi/Réception des mails sécurisés', NULL, 'mailSecJob', false, NULL, NULL, 'PT5M', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 1, 'CronJob', false);
INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (5, 'TDT : Mise à jour des accusés de réception', 'Vérifie la réception par la prefecture des dossiers envoyés via le TDT et dans le cas échéant, enregistre la date de l''accusé de réception et le bordereau', NULL, 'majArTdt', false, NULL, NULL, 'PT5M', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 1, 'CronJob', false);
INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (1, 'Circuits de traitement : Mise à jour des traitements distants (Parapheur)', 'Mise à jour de l''état des projets envoyés au parapheur pour validation (délégation dans le circuit de traitement)', NULL, 'delegationJob', false, NULL, NULL, 'PT1H', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 2, 'CronJob', false);
INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (6, 'TDT : Mise à jour des échanges de courriers préfectoraux', 'Met à jour les échanges de courriers entre la préfecture et le TDT', NULL, 'majCourriersTdt', false, NULL, NULL, 'P1D', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 1, 'CronJob', false);
INSERT INTO crons (id, nom, description, plugin, action, has_params, params, next_execution_time, execution_duration, last_execution_start_time, last_execution_end_time, last_execution_report, last_execution_status, active, created, created_user_id, modified, modified_user_id, model, lock) VALUES (3, 'Signature : Mise à jour de l''etat des dossiers envoyés au Parapheur', 'Met à jour l''état des projets envoyés au Parapheur pour signature et rapatrie les informations de ceux en fin de circuit.', NULL, 'signatureJob', false, NULL, NULL, 'P1D', NULL, NULL, NULL, NULL, true, '2014-02-26 15:36:02.936482', 1, '2014-02-26 15:36:02.936482', 1, 'CronJob', false);


--
-- Name: crons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('crons_id_seq', 8, true);


--
-- Data for Name: deliberations; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: deliberations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('deliberations_id_seq', 1, true);


--
-- Data for Name: deliberations_seances; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: deliberations_seances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('deliberations_seances_id_seq', 1, true);


--
-- Data for Name: deliberations_typeseances; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: deliberations_typeseances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('deliberations_typeseances_id_seq', 1, true);


--
-- Data for Name: historiques; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: historiques_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('historiques_id_seq', 1, true);


--
-- Data for Name: infosupdefs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: infosupdefs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosupdefs_id_seq', 1, true);


--
-- Data for Name: infosupdefs_profils; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: infosupdefs_profils_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosupdefs_profils_id_seq', 1, true);


--
-- Data for Name: infosuplistedefs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: infosuplistedefs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosuplistedefs_id_seq', 1, true);


--
-- Data for Name: infosups; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: infosups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('infosups_id_seq', 1, true);


--
-- Data for Name: listepresences; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: listepresences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('listepresences_id_seq', 1, true);


--
-- Data for Name: modelsections; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modelsections (id, name, description, parent_id, created, modified) VALUES (1, '#Document', 'En dehors de toutes sections', 1, '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelsections (id, name, description, parent_id, created, modified) VALUES (2, 'Projets', 'Itération sur les projets', 1, '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelsections (id, name, description, parent_id, created, modified) VALUES (3, 'Seances', 'Itération sur les seances', 1, '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelsections (id, name, description, parent_id, created, modified) VALUES (4, 'AvisProjet', 'Itération sur les AvisProjet', 1, '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelsections (id, name, description, parent_id, created, modified) VALUES (5, 'Annexes', 'Itération sur les annexes', 1, '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');


--
-- Name: modelsections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modelsections_id_seq', 6, false);


--
-- Data for Name: modeltemplates; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modeltemplates (id, name, filename, filesize, content, created, modified, modeltype_id) VALUES (1, 'Défaut', 'modele_arrete.odt', 66588, '\x504b0304140000080000d57c6e445ec6320c2700000027000000080000006d696d65747970656170706c69636174696f6e2f766e642e6f617369732e6f70656e646f63756d656e742e74657874504b0304140000080000d57c6e448c34972fc6130000c6130000180000005468756d626e61696c732f7468756d626e61696c2e706e6789504e470d0a1a0a0000000d49484452000000b50000010008020000007a41a08c0000138d49444154789ced9de96f15551fc7a7746f292d5de90e6d69e9425b4aa14514112b8868889160481435624c888989ff806ffc238cf185f842430898b884c600a2a12ad4b2a848352ddc5268c12ed2bda52d7dbeb9bfa727a773effdb5f03c7269fbfdbcb8993973e6cc593e73963bd3dbb0a9a92987900084053b03e491867e100dfa4134e807d1a01f44837e100dfa413482e9c7d4d4544848c89497102ff2658c6fa0124d9232d1245909b12f61c7776dfc8f89bb8a63c709541c2533f6155d1b8152f6dd0d5409ae2a9d4b1b05d30fc9a2abd6cc217b5b89e64acd5517ae687e4fb4cf32d7f29b78a040fd2abe8d61ab6037b0eba8df86f72d9ddfe2d867d915e837be42f0fb0f6c0c0e0e4646468687879b828d8c8c783c9e356bd6d8558368515151616161fa7d393c3c1c1a1a8a04ed36908d40b70e42fafbfb636363972c59e2a81d1b129f9898888b8bf39b9484200e36909a39ebae97a54b974a8844433ad836c5712562ae3e3a3a8a98f6b9f89c9c9c4456514c67a67f265738ebdebd7b313131f61d35303080b32463f3a0ff905cc2836fbffd162a6cd9b2c514e6f8f1e3111111797979d2cc08696b6b43606161e1b66ddb4cb45f7ffd352121e1ce9d3b151515a80ed417dae6e8d1a368bf5dbb7641380422daa54b9756ad5a151f1f6f2afde2c58b2604bbd7ae5dbb7cf9727a7a7a7575b59238ac3d72e4487171f1c68d1b7d9f4b485288f3d9679f6ddebc19d1ece2c0bf1d3b76a4a6a6222909fcfefbef11a1aeaeceb416ea2139391971b2b3b31188b6840788969696565959692e84c8870f1f461e501b12adafaf0f45401c242e677df3cd373939391b366cb0ebf9bbefbe4325e7e7e7cf0f3f041409f707a4b60351cba8292862427043200495ee782b486a796c6cecfaf5eb763454cdf8f878575717d2341d12ee9bdede5ed860a78f98cef4edd8d9d9b972e5ca9e9e9e59134736962d5ba61407578f8e8ececccc74ac6e1c99911bd799eee7d19da0c1b08b0d5c42b2818d96961674152663434343d8c0cd8314d0d348f3777474a080cdcdcdf0c3640c253267210e3280ccaf5bb7cef44fb810a2e999f725f8f30fb4fadebd7b510cc7aa50dc0a68543406ea5a4256783177bc0c04656565e81b4a4b4b1d6fa5e0133d2a76110d279abb04b7e3eddbb7d16198f40b0a0aa4b5e4ac4d9b367df2c927a84dc75bc58112479ac8182ada77d2604087b77dfb7634833d557ce289275040190e04c8b77bf76ed9100511adbcbcdc141957840d480725fafaebaff7ecd9237da1e31d920e1c3880fa11691082b2a04472965c173d1f4eb46b15b5079f3046a7a4a4cc8ff98780b1c0772e29ad6287a0c9ef793155e0786d40d3dad1d006353535264402577ab1a3a18eccae08f1faebaf9be1602e893b8e7b962749c18fa4a424d7b2422e17622d37506a23a88996989868479364714599a99810a48608e8664c4c5cd45c42e2a0cb71e513aa6170f19b7385e0fb21d8779bf4f0d26ca624aeb599f914635c21a68331a939335bc2effad044b8afc45d45f0cda73daf745dd199391b75459308e15e5c67d945f04ddcf18e388e5768577124f3f363fe111278dd250de08ae6bb8b4fd369fb869840939aef855c37abbd3bc7c4e7924fbfe1f6e9813266c7b733e6babaefe9bef9f42dce5c7854fa8f2012a82183cea39031fa4134e807d1a01f44837e04017b55652fd0945dfb4467e65ac96f4cc7fbc5a3ef7aca351f9f15fa11344cd39a969370b3eb5ad0dae7da6b54bf8d6d562bb6490f9049fa11343a3a3ac2c2c2222222a2a3a3f1d9dfdf3f3636b67cf972f98a0247112735351571262727f1393838880d44181a1af2783c252525adadad050505386b7c7c3c2a2aca3cba436a08bc7cf9f28a152b902c3a92a2a2a2f6f6f68c8c8cc8c8c8fbca24fd08027237373434a05db3b2b2d084b8d7fffaebafa4a4a4b8b838e8224f61e2e3e3116178781881cf3ffffcc0c0c0c99327d1d8fbf6edebeeee469cfafafa83070f7efef9e7700206343737e328044a4949d9ba756b6f6f2f9c90afd81172ecd8b103070ed08f7940c8f417de9d9d9d0909093d3d3dd82e2e2e860d7d7d7d30233c3c1c72c4c6c6a2ab4003631b72a069d1494c4c4c40029c8826874fb067d5aa55388473e1597272724e4e0eec41fcaeaeaed2d2d25bb76e211cdb696969e854ee37abf423686cdfbe5d5e1bc018813e03630a6c802bd9d9d95bb66c812221d3cf1946464630b860b078e9a597e42bff9d3b7742230c31f2f0cff13a57535363e625d878e1851730d014161642296cc09540335905fa1134cc731cc82153c8182fd8801c260e88f5e278a7a5728a795a694f6ce52c33ab95079f00bd8e33fd4a91e3ef7d3605faf15f7c1fd987f83cc6fbbf5fd47701625fc8effb038279d66fe32a82cb1efb91d6dc59a47eb89e12fb3e533531ed55a8e3ad74f34457797eebf772f697104ee075a9926dfd598cdfe785f77b09178bd10fd7337dc7aa327999cdbc3376f7ee5d4430efa1e113d305d74376d76b03a29dfdfdc4fff80d4470598c7e38d3dd406363e3b56bd75e7ef9654c00c3bcb4b5b59d3973e699679ec1fc1f33bb53a74e3df5d453e2075c191d1d3d71e2048eca6b6c38cb7e0118ab0379c702f309c4c45990490c434c79a32cd8e5be6f16a91f425353139682e7ce9d6b6868c8c8c8c03a10733aac140e1d3a8415e3952b577efffdf7f4f4f4cacaca0f3ffc10f33b2c342004569238f7a38f3ec206d69610024b898e8e0e2c4de5d5765982620381ebd7af47825864d6d6d6969797fb9d373cca2c463f645040cf0139b2b2b2befcf2cb279f7c126bcbccccccb367cf6edcb8f1ead5ab68f24b972ea10b41b8e31d6856af5e8dc5e72bafbc826d09292a2a8243494949e81e600022234144fbe0830ff6eedd8b14109e9b9b7bfcf8f18a8a8af3e7cf8b1f0f364f0c168bd10f01b7fb9e3d7bd09c1f7ffc313a0fb4656262626a6aeacd9b37b76cd9d2dede5e5252824f4470bcefc3e290bc1f2a0d8ca3292929e82470e28d1b37aaaaaa121212e00a869e77de79079d04e2631e8394d1fd783c9e5dbb7639335feb9a172c463f64f2287f2d82ddb7df7edb1cc278811101ad8b3b1e71d097c87050575767e2481b3ffdf4d3b2eb7a8eea785f7cb7a71a8f3ffef8e6cd9b9535cba3cc62f4c3f578dd5e6ed84b5913d9ac501c9fe7ef8e77aaebcc5c7962a6e2da75a6bf0d9b77ab98c5e847c8cc9796a53fd0ef6fdff7a59569846b10515e697ef4598c7e90b9433f8806fd201af48368d00fa2413f8806fd201af4232081de18b2df1b32efe0ccbb2fbee608fd0888fd1da8fdfb0b53d33f2033ef1ec63e00f443c3fe26def5ca8f840c0c0cc4c4c4ccbba76e73877e04449ecb783c9ed1d1d1eeee6e78505555d5d5d515151575f5ea55846cdab4a9b1b171dbb66dc1cee9bf08fd9805741537bd646666fef2cb2fcdcdcdd1d1d1c9c9c9636363a74f9feee8e8282b2b4b4d4de5fc63f16266a3f26632e490b750e52f0fe4af0716a41c0efd50902647b7217f798691253f3f3f3737777272323e3e7e626202117a7a7ae2e2e2829dd37f11fa1110f1232222223c3c1c938f848404ec262626864cff4e213eb3b3b3edc80b0ffa1110fbab0ef31290f9b31713ceef3f1629f61b4381fe3049ff83a50500fd201af48368d00fa2413f8806fd201af48368d00fa2413f8806fd201af48368d00fa231273ffcfe1abc12f8ef66993c44343f4cdb4f4dffeb32fb371142434365d7fea58307f8854df228a3f9611e5dfa6d6cdf9feef3fd376f64be33a7fea3bebe3e3535352727677c7cfcecd9b385858550a1b8b8d8e3f1c87ffe9d9c9c6c6d6dcdcaca2a2828907f2440451606b3cc3fa4a58786860e1d3ab47ffffe8b172fca7f1468696979fffdf72f5cb8d0d4d4545d5d2d3fb175f2e4c9f7de7b2f3d3d9d7e2c1866195f64f8a8adadcdcdcdcdcbcb4b4949915fef5bbf7e3d3e2b2a2a1088fe232e2eaeaeaeaea6a6262929c9e1f8b28098d3f892999989b1031bf2037e06c861e29817331dfab18098bdff90d58a6bfd22bf342dbf5fee4c0bc1f5cbc26396f987fe5b6c81c229c78281df9f120dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e100dfa4134e807d1a01f44837e2c1ca6a6a6424242f0694264179f0f9c26fd98df880d30e0debd7bceb422664302ed68b63d12a23b443fe63d76031b272470c9922522846c48345f21940e867ecc7b20049affe79f7fcecccc8c8b8b6b6b6b1b1b1b8b8989494a4a8a8f8fbf74e9d2f2e5cb333232c6c7c7232323878787efdebd1b1616161e1e8e73a3a2a2626363d97f2c6420075afdfcf9f37ffef927b63b3a3a56ac5871faf4e977df7df7a79f7e42486d6dede1c387c7bdc08cbffffefbe2c58b2fbef8e2c0c0c0ca952bb76edd0ac3424343fd26fe30fcb0e74d76171728a6dfc846703b35bb3b757cc6573b9a49d01cc28992b849d69519570fec2a857d513b0ff615a587777c867ffba2268239c56fce7d03ed6a41ebbef1c61be836b031313181be61c78e1d09090905050552c65dbb762193d068e9d2a58830323282ae051d49444484e3352c50db3dd4fec394677272d2b48d6927a92cfb908c9a1262aa1b9fe23b3e4d886b3a665fc599ee819d9915816d57eddb429804cda58dac72ae7d7533ccdb36bb84b6c3edc82ebfedecb9d231f9b4671bb28b8103e960b030d1306ad8a5c32e3ee1845c0eea48a0cb575f1e9e1fb01b9f5d5d5d29292928091446a94461e472747414cd89382803f4c706026fdfbe9d98988831d5ee3f10333a3a1a27621be7f6f7f7e3b658b66c198c418288897351b9b84b30069bd919025135fffcf30fce450ab88d6edebc896d74c572747070101bf894aa446aa84474c8920e76250fc824d241299049e40d27e2a24816818886f838845c61c81f1a1a42079e9696866451585c343535b5afaf0fdb388a1ac029dddddd983438d31d0c0ea138c81b8e227b482d3d3d1d81b828d2443812471e705da48ccc2305043a56f7e3eae7b0811cdeba750b57c1c862844386b18bf491675c2bc8f30fb9dd1b1b1b9b9a9a502ad4fb8497bababa13274ea0a2d7ae5d8b625cb972e5ead5ab38ba73e74e3881ba3876ec587e7e3e52c8cdcd457950538585859f7efa696969695555d5575f7df5dc73cfe1148cb508474ba090685d5ca8a4a4a4b5b515f50817d13038b7acac0c091e3d7a342b2b0bf9d9bd7bf7850b1750b96845c43975ead48f3ffe88c640878c36c3f08cbce1d298eba19d90196cfff1c71f655ed0848873e4c891c71e7b0cf171a8a1a1a1a5a505f3c1679f7df6b7df7e437c6406b9c5369c4063a0b028426767677d7dfdead5ab8b8a8ae025c27ff8e10784c330cc03f2f2f2503f3805ba1f3c78f0faf5eb5f7cf105a60ee5e5e5376edc4011903e4a8af926869273e7cea12088b97fff7ea964d3d3d8a31e3e3d1e0fb28a1a860aa86ab8525d5d8ddb00d314d40fee2bd45590fd906b4361e4a3a7a70775819954727232a4419965b20d2d704f60177d066e17997ebff5d65b68d43367cea08dd1afc81d809a4582d845eba2c651ddb8b7eedcb9835b1321d840b17114d587d64552481977b0f4a56bd6ac417ce8855b07f58ed3d19638843a426da29d366cd8801ac42d95e9456e533424524086a5678af30223f1890be112393939c821767114294074d43b4a8a5503fc8001ededed481fd1d6ad5b0723912514109171274053336f10692016b2875a82fd48137980c1a81644282e2e46ca900f57c11591c8acd58e3a4135e26e419d234bd9d9d93234c3151413217adb3d0c3f64d4cff32221a826d9c0bcc974866851c79ae5a122b0812aaeacacb4057ff3cd3765e3b5d75e937094199fa82f9459060249c7844882a8eb7dfbf6c936aa06750d2d2429e918e410b2813b4c5240c33801e6b9afbefaaae9ccd77a313315e41971902b738a242ee535d58202e228ec71a607887c2f720a720b41cd154dfa92a6d4a43d17f1458eca6d09a56a6a6a7a7b7b5122ec423ec79a0e0679fee19a9c3b33a76c32793487cc5cd2991e95cd24d199b9ee900d89804f997cc8b4518ecae4dc351776662e1f8c8eae75871ddfb15ac2d57bbb4e772d464c11ccb9ae45936b42ed4ac714d01cf55d28d9f78352edb20d39501b7626676dbb8737bef84ec51dab547ef3ea37d0acd4cd865989d8ab03d75787be374aa06dbfd7d20fd9cb22bd44ae045d715ceb4cdfabfbcd8fd27fd89f66dd672ba8a7e02c8cefc75c6daf6c2c4e5cf7e77dd5c642f083fc7bd00fa2413f8806fd201af48368d00fa2413f88c67f008f0c76731f56f1d20000000049454e44ae426082504b0304140008080800d57c6e440000000000000000000000000b000000636f6e74656e742e786d6ced5a5f73e3b6117fefa7e0b0d34ed30945519265493d2be35c274967e24b277666fa76039310851c08b0006859f946e9533f43ef8b65019014ff89a265c7b976fae21bee3ffcb0bbd85d40f7e68bc7843a0f5848c2d9951b8cc6ae8359c823c2e22bf787bbafbc85fbc5fa776ff8664342bc8a78982598292fe44cc1bf0e6833b9b2dc2b37136cc5912472c55082e54a852b9e625668adaad22bb396a548b5a783d58d70555be147355459cbd674d1fdf0958d70553b1268375459cb8253abea1b3e54f951526fc3c1eb498a1469a078a4847db872b74aa52bdfdfed76a3dd74c445ec07cbe5d237dc127058caa599a0462a0a7d4cb15e4cfac128f00bd9042b34149f96ad426259728fc560d720855a51950ff1e08c78888fb826dc223138378c703dbcd3687878a7515537416a7b24260bff0698e6cfcdb7875c10c9d0b5b46ccd55a120e9e06d5ae9aa3ee7bc84aa15ec01357027e3f1ccb7df15e95daff84e108545453cec150f110d4b8ff3a4cb692017f820e1e1079da665e26b47c8230a13dfb24b61191d35fd8f9b6f6fc32d4ed041989c16f608930ab18367840ec2d19d5ef802a75ca8d2319be10513a23529b16d55428f1f77cd2d446311459da20067eac3d18783e73d10bcfbbd5babe4fd09b16c2484298ba7548c50b56ef62a04635fcb94c71852e450e4455cf6a10dcf186c027a57ee40fc986241340b51a3b6aa59a8663de56798cc7b57c542ada0134c8b8a516ea9d30ce75e222181e0a0f07455d1aef707913c0e33a70f078f364d8b8d42114a39555df970f7bdaf799e6e8fd000f2952a63c1c45d173380ad1fd22f091b9805bc0d0ab117e190caf51b5bcb4bb263bf35ee2bf73bd8c8ed3eb9e790a450b70ba984d07d8de9f79bb9435b9ea0a0c386e59cd407d749e71dde39df8334ebb0f3479472f997869c25ba4ecdb496f762cc201e50e9446eef20911215422f78408298437002da35887539a7a01f5f5aee8894cf5ebacba9edb551c4053343c395fb0ed2ec55a0393f3002b329766e6e8f46ac29382064722f154e9e83eee6d6b9212cdcf2a3b04a8957c1931f82a3a7e3f9abfbc70e7f4e4799828514093d63a7ac0ae66f032b98445950ae9843cd3b862526302062e1a528c69e55bb85d61b2111b9856523efa550fdb050044ead25ef48a427b160319a2ec3725bd6503ea56aacae63a77b44490c152f412286f66e765a017e7a17a3ebce7dc07c4ab38435c05a621b734ecfa14f47c1c5ec005d60ead505826032bdfcf31958bf7c71ac8bd1e572d187751a4c2f8273b0be7d71acf3d1acd7ad93c97276965bafbb73d90b31a54da440aae2dc7048cdc8debec7a3f1f2520304e23d17914e55c6193e03d1ac1b90e0bb061ea0b4dd961066185b4ce22dcc0f93d1621e00b0333c7304c800cf58de83fe8261ce2bce29810917bb2fecb7bfb7029822816281d2eda18008fd0e623ef28af40d46b056b98d52a5115f3d6315f061d42f0cfe984945367b4fc21ec0e40e805fb91b44e553704fcec16da2f37e327effd63eeac8276d21c4ccdcf686ec228fafd6ae1b345d44929f00cd2255b5dea3a91e5c91103bc2d3377f8a1f2d77b8aba6afefaac1d1fe94fcd43ab1ff7d2915cc7a7cd5cd2c9d65d8c3bd75f13fe0ad799fb73a99076fcd9fe4adf9eb78cb0e731ec51b685ee3bc33e444615b5a417dfa712db5088b004d6ecaaae9c1d2ab313fc9237ef91b84c1832e1d2cba63319a0797af1d8f81ae5a9ce3aad655e5a512f5d7d8e25d7b78056b6e4fc652acf4dd4ca628cce7afd9b23258e7ec0f5830c37e2a9ed64cd38ba77cc05c09492029c6e3f1f4329c47c317dc8896074cac48d813e2afadc4e110e42aed095664cc535bc1b318ae18f728fc100bfde258d8de81de955b112a18f6b2ea697e8a23af4c21a9875b8f92849405a11c92532ef57e78e2299eb6b870e3e9c8df2d17e427ae9f152bea3a233b04ba2d2444085eccdc3a41424af49e70a8fe0499fab953fbf399ebe8a7de15dcbc08d3afe910b33fe434fd2ba3809b7f85247054f98a05c6acf27d4fb3aa7e8c920441571a572c522ee068944f9b79d1304c92e85701aef358ed3bd4121e8175599ce65646d910e99fb3b0d7ceab7797d0ee7291c3eb487e3f5122c36ec5c2de2f3f7486af9d377ef5b3e025e0a06d61d42c76e552cee272212d9d219a2f30cce81e23d16553efb7b5c5c34b50fbc52767dcf3685f7ed895cccfb012ff33c3ecf072d4263a8614119952b4f778a628611892f141e7ddd8b56cebddbf510a3d4198877b8df459c6ee8a57aee759d175eab946fe6a7f3636ce3feeb54c426930bf1ff491cb777d04c7c453fbd464b3d0e60b96fdf48c049ce02cc182d7f058d2fb085302895077f77397d32629fdf8736d417eff2356efa18cc23f2fb69236821d142a5c5bcb905b7bf38fb9d83c189abff9e361f341d3525b5393e6d5d4f387a71ef9d1b5d9fb9354be7cbacadba68ae0bb860d88502fcec0ed89461ebed43abd7f9c349e6facfc9258724dc82c059d49b795bc087f3a109f8fc5ef0de6ffa3fb6946176e6445db4811ebc03a71d737003385f50916456700d9f5e70e5423abab9b743e81981ffaab16cc30524a156571320e66de78ea05b3bb60be9a2e56b3c96831b95c4ea78bf9c25d0733072e23d2d162f99a5abba890e96b65605e6ced406c8b9ad4d70fc6749ca767fafcc25d5f0bf1f15feae3cf0efbcfbf9d56a789b13ad509d796d86e19a0dbef264b09390ca630dd1f387e2feba473fb5c396b36a153ae9ffd5aae9fb9edbedef0766d0c581793c26fe8e82aa5676b8b7266e9e69edc78c74cb23e8c2f271d501bbbfdda445e7c35ff53e7fa17504b070860f7ef35f7070000152a0000504b0304140008080800d57c6e440000000000000000000000000c00000073657474696e67732e786d6cbd5a5173da38107ebf5f91f17b4a42d29430493a8694969604064833d737612fa043d67a2439c0bfef4a863407f84a0dbaa74c6c7957bbfbedeeb712371f1789387901a539cadbe0fcdd59700232c298cbc96df0346c9dd6828f777fdde078cc23a8c71865094873aac1185aa24fe873a9ebf9ebdb2053b28e4c735d972c015d37511d5390ebcfea6f57d79db2fcc9427039bb0da6c6a4f54a653e9fbf9b5fbc4335a99c5f5f5f57dcdbf5d208e5984ff65595af7eab0a115f15d90ff2cd3865d5b3b3cb4afe7f70b2dae41bd75483bbb51fd6e6dfddac14e47f4eb981c4fae664f5d86eed362095f5170ef357af05bbbefbf737df697da8800d310dd66fcc32a53702e524b83bbba96c8bd85f6c07c6c687dc671e9be94ec1efcfdf5f1c28fc0bf0c974f7b6abe71f2ececb491f4c71de87983006cd299313d01b1a468802980cee8ccaa09c8eb66c289c6b78c0188aa48f99d07b8b3f4d587aca650c0b88b79db51b60ee1b4a0db5dccfe5ed7863abda286e9d6db15c2d1fca42ec9dd7aa1f0e804851aad42e6a1fca4ad57c24e0f8c9e2c41e3db59dd47e618e5006566b07c96ea03198142560f5ba9cf01f88c990446da26d8aca1c54903a6c899969a2c812b999d42be9256bc62fe90dc4d9d1b27adb2f2d161954bbf75e2be99ab61e8080c840dc52f4a0c4ce773c7c5b5c8a5eafead5ee05d41bf7efa6f9834c3143bdf94fda6a8f2a9869b1c5e3b6e1abea56d9534828251aa77f47f80fc3ae93df4472260a0fcdc889efb1093458349b28cce466a13f9612578bac266f5628d42941d943f639f903428480af382a34e0500d9f92d42c3db968c8465de2af0f4c4db8f460c0a76404f160a9e9458bd0eac3474e852fe1bff0df638a5957dd2b367795e4f8ca9ee48828ec8c511b7dccc82ae549cf2ae86381730fd2070913a2c952dd031551b927e05e5d794aeedf05a3bc78a204826c00977c4d10c2c6fff86afa9ac77dc4cdea44a6115faad62eaf4ab28f27b96821b9ff0728fcb4301d60765cf710eda6e069a8692a524443408532a2be06f1b3a2a5aa25968e3ff800b1559c42dce391c994170df7f88886806ce55ba07547ffe8aeb485d883b20eb2b84f514229961ec4b72792c24289af4319370493334df8e8d881968928138ea5f8f2611f887ed9fc098d51562f55375bb07de9fb9a69c3c74b6b9d7ee666fac064c644c3d6565f3d9a5c1acd7c35a1270de4b404d41016e659b134f593cb161e7db05878a1c9b34d54bc3846e54b5e18bb5e3a485944660c7168fb9dadb07ee80d8dbb38ff06b039481f29305d113b001087029a0e22aff1b7885ef9cd4ff843630f32f2c8934a1ba709e16daadbb2c3b58f840d05b110e741a2d04d262310c787411ea7574ee5c38c38a6360b4a32f19b5e5bde8a9640668c63879838c2ee036c834c39bebbae358ecef9e848bac34620ee5787f1be0a731fecf13c148eaf07f7d41657dad8c4ccb3a62d7dc2ecb5121017a199b6879adbc6ed4799f39faba0be667350d669f64a863c971f96159c274ff9647a4a340f45660d2e096df602dff3cb9aae6c0ad43e885c93f287acb96794a94c431393846d9d9eec7f90b421cefe1d60a6a2f24753ddccd88b8c0e2586f89b8388b54fc43651d288f38bba7425419770fbbf797ec7b1b19bae4a1ebcadae817a0a0d252021e91b6cf175527e75d9e092a9e53e01b133e0ae1d5e5c56afaf2f8e70f4d51f767c9507500330d916b7fa430f84824f24559a81c1745dd37c941c17335b407d7403498dc6345011d8fd71b41579765579a5253403c39407a2fe3ad45326a5547b6ddd3cfa254c98a662496d4dd9b4f5366cbcf5971f47993033f894c6cc143695838fe353502d85c9ce8c3b02b65ab6197837e30b8fa9dbdb76e085547e230e1e6ace642f9391c97c9d6be4dc888ce8d1e00f531494f5bed47cb613d87f4c96e50332304b22778f780f6396092fe35d9cdf2076c7630d7e0e471c586d5e0c21498517e03e30336d504fb39ddff52a9f130ba8836e1e691499e53e29be742c79a8ecb6670f083c5d46597efe59e088bd4e85d69832a029bc3dae6cfd3aab52f4bbb5bb9f504b0708a8dba4ded7050000f9260000504b0304140008080800d57c6e44000000000000000000000000080000006d6574612e786d6c8d94cb8e9b301885f77d0ae4760bb68110b080513723559aaa959a4add458eed104f898d8c19a66f5f73719ae964911d9cf31dfd171bca87d7731bbc08d34bad2a80230402a198e6523515f8b97b0c73f0507f28f5f12899205cb3e12c940dcfc2d2c045554f16ab02835144d35ef644d1b3e88965447742f908b9a6c95c68515e5ba97e57e0646d47201cc7311a93489b06e2a228e0ec7a94b30bd70da69d29cea068c554a18738c2d0b35387f73635b1d72d69ad2f85267c697a2e172394c2e5ddd38de1bcbd35806313e83aa496862f528c1f41b08e7fb5f018d47ebb531b753937c38ca0d611a10b8b3a463809310ee3628737649393348bd2ac8437d0252eb8b4ee04433e98d9abbfeff0578c7eac9177f6db14fbc35ad1d7dbffe8555ed84628e1c2dad44ff260c4b77902984671e40ee1d39354c3ebfe579eedb334b802f69dd1cf82591867146fe3841db7ee214179c28b0d476e5f49c10f5986282e708a0ebe857fd54ace88df491a22b79674873392e4046da3cd3649709e235c428f4dfcbc21977d6cb59154059f9fa599096f2c235d2e776fdd567a2b5930eb961e5a11323d285b81042ca23cd3e622e255d4876938afa255ed6e901d35b431b43b5d0cef8cda702f6eb2556427c7332b8c77e24dba5aca9dfc789256f41d65aece3b12e500ae130ebdd3b9384a25f89a765f4505bea8a30ef07d587c1f96dc87a51306dfdc7f78eb5f53ff05504b0708642c5ba30c020000a9040000504b0304140000080000d57c6e4427e348f227c6000027c600002d00000050696374757265732f31303030303030303030303030313645303030303031353946343731323043412e6a7067ffd8ffe000104a46494600010101012c012c0000ffe123fa4578696600004d4d002a000000080007011200030000000100010000011a00050000000100000062011b0005000000010000006a012800030000000100020000013100020000000b0000007201320002000000140000007e876900040000000100000092000000bc0000012c000000010000012c0000000147494d5020322e362e320000323030393a30333a32302031353a32383a3435000003a001000300000001ffff0000a0020004000000010000016ea00300040000000100000159000000000006010300030000000100060000011a0005000000010000010a011b0005000000010000011201280003000000010002000002010004000000010000011a0202000400000001000022d80000000000000048000000010000004800000001ffd8ffe000104a46494600010100000100010000ffdb004300080606070605080707070909080a0c140d0c0b0b0c1912130f141d1a1f1e1d1a1c1c20242e2720222c231c1c2837292c30313434341f27393d38323c2e333432ffdb0043010909090c0b0c180d0d1832211c213232323232323232323232323232323232323232323232323232323232323232323232323232323232323232323232323232ffc000110800b800c403012200021101031101ffc4001f0000010501010101010100000000000000000102030405060708090a0bffc400b5100002010303020403050504040000017d01020300041105122131410613516107227114328191a1082342b1c11552d1f02433627282090a161718191a25262728292a3435363738393a434445464748494a535455565758595a636465666768696a737475767778797a838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae1e2e3e4e5e6e7e8e9eaf1f2f3f4f5f6f7f8f9faffc4001f0100030101010101010101010000000000000102030405060708090a0bffc400b51100020102040403040705040400010277000102031104052131061241510761711322328108144291a1b1c109233352f0156272d10a162434e125f11718191a262728292a35363738393a434445464748494a535455565758595a636465666768696a737475767778797a82838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae2e3e4e5e6e7e8e9eaf2f3f4f5f6f7f8f9faffda000c03010002110311003f00c9a28a2bec0fcec28a28a0028a28a0028a2b77c1b147378bb4d49515d0c84956191c2923f5153397245cbb1a5287b49a8777630f18a057a478f208b56d19b53863513d85d3db4e5473b73c67ff001dfccd53f015bc5a769d79af5c46ac43adb5bee1fc448071f98fd6b9962d3a5ed2daed6f33b1e01ac47b1e6d2d7bf97f5a1c1515e8fa84122fc5758ec84114922039922dcb9d84938e39e2b99b0d026d7b53d4ddee60b686d99a49e62b855e4f451f43f955c31316b99e8ac9fde67530728cb963abbb5f77cce7a8aeaedbc25a6dfceb6d65e25b59ae5f3b2330b2ee3e99acfd2bc3573a9ea57968f3456cb6418dc4afc840a707a75aa55e9d9bbedea43c2d54d2b6fd9a7f9189457596fe11d36fa75b6b2f135a4d72fc2466165dc7d335474af0bcd7f73a847717515a45a7e7ed12b82c1482470075e868fac53b377dbc987d52b5d2b6fe6bfccc1a2ba39b41d16386474f13dbc8eaa48416ec371f4ae72ae1353dbf26675294a9fc5f9a7f9051451566414514500145145001451450014514500145145001451450015d07823fe472d37fdf6ffd04d73f4e4768d83231561d083822a2a479e0e3dcd294fd9d48cfb34cf45d2ee23b9f15f89341b86022d41a4099ece338c7e1fc8543e212ba3c3e1ef0dc4c0b45224f71b7bb16e3f52dfa5701e63f99e66f6df9ceecf39fad2bcb2492191dd99c9c962727f3ae6585f7d3be9dbced6b9daf1f7a6e3cbaf7f26ef6fbcf4dbaff0092c56fff005cc7fe8b3595e1743743c5761115375711b08a32705c82f9c7e62b8833cc65f34cafe67f7f71cfe74d0ecafbc310d9ce41e68faabe5e5bf44bee77078e5cfcdcbd64f7fe6563b4f0b78575bb1f12d95ddd583c3042e59e476180307dea6d0e449afbc652c6c191e09d9587420b3106b896bab8752af3caca7a82e48a6248f18608eca1860e0e322aa54253bb93d5dba7677ee4c3150a7caa11764dbd5f756ec6df82ff00e470d33febaff435b1692eb7178a35e1a45925e44f7122dc4322865237b632091ef5c5abb2306462ac3a1070453d6e26466649a4566e5886233f5aba9479a4e5dd5bf133a588e4828eba36f4f4b1e8eba73dde95a99d73c3961a6c515b33c53c3188d83f61906bccea592e279576c9348ebe8cc4d45451a4e9deef7febcc589af1ab6b2dbd2efee4828a28adce50a28a2800a28a2800a28a2800a28a2800a28a2800a28a2800a96daddeeae63b78c80f230519e99a8a9f0caf04c92c670e8c194fa11533e6e57cbb950e5e65cdb1d0db783af66f2cc9208c3c464fb87e53c614e7183cfe94b7fe146b0d31eeda5662912b32f030c48047bf07ad771a65ca5dc16b2cedb56445672bdb22ab6a96716a76cd6b248eb13303b90e0f07debe2967b8af6c9549251beba79ea7dc3c870bec5ba71bc9ad35f2d0f2baea3c3fe1db6d56c96e2576c02eac15b073c6dedf5abd0f832002d8cd212416f3c07ea3f876f1f4ad4d274b8b4789e346dcee72cc09e464e3f4af4332cf28ba0d61e6d4affe6bfe0fdc79d9664759574f1304e36ff27ff03ef33d7c1569b53748d9f270f87eb271c8e3a75ae4f57b24d3f5392d11b708d532739f98a827f526bd4a5b88992211a95655c39f53eb5cd5d7856deee5bab857c3caa1a3058f0fcee27db35c59667528d593c554bab69eadafc8edccf248ca92585824efafa5bf538fd32d05fea56f6a490b238048ea077ae84f8227db1ffa46098cb3fca0ed6e3e51cf3df9abf0f84edadafe3b8596411c611970dc97079cf1d2ba989a268e532390e07c807735ae3f3d97b48fd565a5b5ba32cbf218fb392c5c75be967d0f32bef0ede585a8b89b6ecf2848dc1f949206d3c75e6b22bb5f1adf48b6f6f68a709212cfef8c62b8aaf7b2ac455c4e1955abbbbdbd0f0335c3d1c3625d1a5b2b5fd428a28af44f3428a28a0028a28a0028a28a0028a28a0028a28a0028a28a0028a28a00f52d116396dec2104ac6c88bc76e055dd5a04b0bc68431d9b41058fad6068da3eb579a3dacf040ed1327c844aa381c74cfb55a7f0beb9206cdab0623eff9a848fccd7e655f09554e71e4d799ea7ead87ab49d284f9fecad3e4584bfda8c1641875c1f71d6a3f383331073cf6a6d9785f5bb6856392de49987f1bcb1e7f9d5e4d0f555eb62dff007f53ff008aae5960f117b72b66d1c451e54ef6658d54d925bda1b52a5cafef369cf381d7f5ace86f0c126f560181e09fa561deea70787f53bab3bdfb59b8660e518ab04c8c80307a73501f12d949f7526ffbe47f8d744f2fc64e5cf1a4d2d363058ec1c7dc9554ec745f69566037af1ef5ac2c906886f8bb6f2dc0ed8ce2bcf0dfac979e7ac973b0ae3cac2edfe756935170bb51660a7b0603fad38e5d898df9a9b7a7dcc16370d3daa25afde88fc64519ed0ff1e1b9f6e2b96ad6d7659669a032060361dbb8827ad64d7dde4f4a74b054e13df5fcd9f9fe75523531d5250775a7e4828a28af4cf2828a28a0028a28a0028a28a002ba9d3bc31629a3c5ab6bd7ef676d31c431c69b9e41ebfe45616956d1deeaf676b2b158e69923623a80481fd6bd03c4fe1fbbbef0cdac70ab3dce900c324607fac4c0c3a8ef9001fcc76ae4c455e594617b5fa9df83c3f3c2753979adb2f3ff00863193c1fa7eb5617175e1bbe9e6783ef43731ed27d81f5aade09f0dc3afea5730dec7288218f2591b690f91807f0cfe55b5f0d278eced357bdb8b86482dd0332678e8496c773f2e056efc3eb886f2db56bb893679d7acfb7d010081fad72d6af529c6a453bdad67ea77e1b0b46b4a94da49bbdd747635dbc21a03597d97fb2edc2631b82fcff5ddd7f5af2df1a787ad3c3ba8c305a4d2c82543215900f94670067bf7aef7c6f6cda98b4b186f96ceed5bce85a57d8b2762037f78707f1af29d59ee9b519a3bcbd3792c4c63337985c1c7a13dab9f073ac9f32bbf2ff87676e3e8e1649464d4755aeb74badacb5d3a0ed234a9f59befb1db1413146640ed8dc40ce07bd6c6a9e149b47f0fc13dd4320d426b831f961c1daa064600ce7eb9e2b174a86fe7d4a15d304a6ef39431120afbe7b0af75490d86936d1ea37b18b92823334842869369ff00035db8bc44e8ce36d7cbaffc31e665f83a788a73e6ba6baf4ff87327c2ba8d9d8f83b4dfb55d4511f28f0ec01fbc7b55e1e2bd0cbedfed1881f52081f9e2bcd6fa39a294c771309a55e1a50721cfae7be6b3678dd5371460a7a1238ae378484e4e4dee7a90c6ce9c1412d9247b7dbdd5bddc7e65b4f1cc9fde8d830fd2a6af9fd2f2e6ca7135acf24320fe28d8a9ab52f8ebc4a9198d754931ebb173f9e3350f2e937eeb2bfb5a115efc5fc893e24ffc8f177fee47ff00a00ac083a0aaf7377717d74f71753c934ce7e6791b24d5a8a3758d1d9182b676b11c1c75c57ab1872538c5f43c394fda5694d75772fc3daafc3daa843dab4511a360aea54e01c1158cceb86c57d5a32f3599f2cba2a65f1c00371ea7b7d6b5758f015e5a5cdd4d6ab8d36188cbe74b22f65c95f53cf19c5749e12905accd73737b14367e4943148e06f62dd71df1d3f1a9be23da6af369f1cb6324ad66a08b9863efdc31c75158ac4cd558d38e8bfafc4b9e0a9ba33ad24dbecbfadbfc8f24af55f09782746974db5d4ee15eeda640e12518453df8efcfad7955745a2d85f6ada7b21d6e3b5b1b771be396e0a851d7705e9ffd7adb1b2abcae31ba5dd7f5dcc72ca787e6539b4dd9e8efa3bf4d1a7a7e3d8f4cd5bc0fa1ea29bd6c9609541c7d9f11eef623a5795687e1bbdd77557b1857caf289f39dc7118071cfbfb57b958dc2dd58c33a060922065de3048ec7f1eb5c7681a859dbf8ef5fd37387ba90346c3b9504b0fd49fc2b870d89ab084d6ed2fb8f471b83a152a5396c9bd7a5f4d3faf33979344f084174f613eb37a9748e51a430e230c0e3d3a7e3585e20d0a7d0351fb2cac24465df14abd1d4f7a9ce8f79ac78aee6c2dd9a797cf70f2b73801b0598d75be38d3124d245c6e6f2b4d8e3b58988c798e48dc7e8001f8e7d2bd0551d3a918b95efbfe8792e8aad4a73504b976b7e3d7b1e6d45145771e50e8dda29164462aea43291d88af75f0debff00db1a6db49711186e244ce31f2be38254fe1d3a8af28ff8a4bd359fce2aebfc23e2bd22c8dbe8b6aba8bacd2ed8cce10ec2df4ed9af9fc6e3f0b5e2945ea7db65dc3f9a61272954a768db5dba14bc79a549a0dd36a5a71f2adafc34371101f29623d3df93ec45617833c4a7c3daafef493677185987f77d1bf0fe55def8ff0051d223d3e1d3f526b82657f302db6ddc00ee73db9af3dff8a4bd359fce2aaa59861fd8fb2aef5feac675320cc6a56588c1c3dddd7ea7b2ea1a569facdba25f5b25c460ee4dddbdc115cfdf7c38d06ed7f7092da3fac4e483f8366b47c29acc3ace93e641e79485bcadd3aa827007f778ef5ab7b7d6ba75b1b8bc9d2184100bb9c0c9ae18622705784b43beb606339f256a7ef7a6a51d1341d3bc396662b5500b7fac99c8dce7dcff004ae03e22789bedb21d192d5a348250ed23f572010303d39eb5d8f8cb4cb7d534659271792c303799e5d9952cdc63383d715ce69ba5786b58f084a22179041673192690a2f9ac71df00e460f4f6ae8a15e8c2a2a955de5fd6a72627038bab41c30f0b41697fd2dd3d4e8fc15676c3c2da7cff00678bce68f99368dc793deba32030208041ec6bce22f153e99a6c165a42836b126239671976079c90381d6a91f1b6bb1beefb4a30feeb44b8fd053961e75a4e717a3d50a388861e2a8cd6b1d1faad19dd6a7e12d175553e759a4721ff0096908d8dfa75fc6b9b93e14698f2e7fb42ec27f770b9fcf15158fc4c28c1352b31b7fe7a407ff653fe35b27e23f86953735dca0ff77c86cff2c5351c5d3d237fcc994b055759dbe7a1e53e2fd1edb43f12cda7d9eff2a344c6f39249504d7a968de0d857c189a5ea2a1a67cca580e627238c7d38fd6bcafc5bacc1af7896e350b55916170aabbc60f000cfe95d9d97c4f997484865b10f7aa9b44bbfe5638ea4576e2215e54a0a3bf5f53cdc254c342bd472dba76b1ce58e997171ab2e9a8079e65311cf4041e4fe95e97e20f0f5bb78791907efec600164c72caa3907f015e65a7ea1716ba9a6a08d99d64f3327b9cf39fad767a978ddb51d35ad61b4f25a55db2317cf1dc0acf111aae7171d91be1a745539296ece235795a0b9b19931b91770cfa8626bd73c33e234f10e9df696b76b770db086230c7fd93deb88d3347d1f5bbbb5b5bcb8bc8ef769318840da54127938383c1a9f53d3741d5fc632d9db5aea2974ae12436aa8b1a91d58e7a7d6b2c4d7c3cd2a73766bafe86f82c063e12955a71bc5a4ed7b69dee741aa7c3dd1f53bff00b5832db16399121c0563ebc8e2adc1e05f0e40171a72b95ef23b367ebcd6c5c5f5a69ff678eeae5236998451ef3cbb7a55aae3facd56b9799e876fd46827cee9ad7c8c2f146bf1786f4733050677fddc11f6dd8ebf415e2b6fa8dec3a8b5e432b0bb90b7ef07decb7048f7e4d777e34d6746bed51ac351fed21f647202c0230b9207393cd7396f37852dae629d57582d1b8701bcac120e79aeac263709469b527ef3dcc71b90e6b8aa919d3a7ee2db55f79ea1e17d0a0f0e68c3ccdbf6975f32e653ebe99f415c57c47d7e5bb7b7d3a38da3b5c79db9c60c9d4038ea075ebd6bbfbdd7ececb40fed9f9e6b5daac3cb19272401fcebcbf58d5fc37adea525f5dff6c798f80157cbc281d00ac6862e942b7b4aef537af93e32be17d96121a2d19c8d15d07fc525e9acfe71515e9ff6c613f9bf03c7ff0054737ff9f5f8a302ba3f0198c78cac3ccc632f8cfaec38ae72a4b7b896d6e62b881ca4b1307461d883915f191769267ed788a6ead29535d535f79b1e3192793c5ba97da092cb3155cf641f77f4c56b784bc0d26bb10bebd91a1b2ce142fde931e9e83dea77d77c3be289a03ad58cf6f7e4ac667b661b5bb739af54b6b78ad2d62b78142451284451d80ae9a74a3393937747cf63b31ad85c3c28c62e32b5bcb4ec655c4ba7783bc3c5e381d6d60c0d918cb31271924fbf735e75afeb5ac78d4c7158e9771f6389b72aa216cb74cb1c63d7f3aecfc53e324f0f5f45692e9cd7092a6f2e5c0046718030735bba3eab69ace9b1de599fdd371b48c1523a822b692537c89dbc8f370f5278482c54e97336f4937a7dddfd4f334f17ebfa0269d637b6f245f66277aca983347c6064fa73c8f6aec6e7c4fa1dedab4165adc763712012891401f37a31231f51d6a4f1ed95b5d7852ea59c2efb702489cf50d9031f8e715e2758ce72a4f97747a784c2e1f32a6ab28f249377b6cdefd4f4ed3fc377de22b48b52b9bc854ceb92c1396238270303b55c97e1cc6f19d9a9387f78b8fe757fc2da9d8d8f83f4dfb4ddc511111f959867ef1edd6af0f176845f6fdbd41f528c07e78af52956afc91e5edd8f8fc6e1f0d1c4544edf13ebe679eeafe01d66d14bc0897683fe789f9bfef93fd335c8cfa36a9e6ecfecdbbdfd36f90d9fe55f425b5e5b5ec7bed6e22993d6370dfcaa7ae98661521a495cf3aae574aa6b195bf13e67bcb1bad3ae8dbde40f04c00631b8c1008c8c8a9e0e82b7be247fc8f179fee47ffa00ac4b38de69238a31b9dd82a8f526bd68cf9e9a9bea8f0a54d53ad282e8ec68468e810b2901865491d455d87b57a48f08c27c250e9936d7b985199251fc2e49240f6c9c579b45c1c1ae38568d5bdba1e954c3ca8db9ba9d2685a91b5bb8a2b8d5d74fb248fce71850656dc405c919c63b568f897c77636fa56742b88deeee1b9754e500ea4823afa66bcff5cfbd6bff005c8ffe8468f0c59dbdff00896c2daeb0617946e07a3639c7e38c57cee26ac956941773f46cb32ea12c152c554d6d1bdb4b3b77ee6aeb0de24f14982fff00b3ae9a0890088c719c1f561eb93e95d5683e3f924bab6d2f57b19a3bc7658bcc55ea4f00953c8aef000aa1540000c003b5729e26f1b58e8378b6c2dbed576a32c0100460fbfafb52e4f67ef391cd1c52c6a5878d0bdaf6b3b58b1e23f0669fe20633b1682f36e04c9dfd370ef5e41ac69175a26a32595da80ebcab0e8ebd88af75d1f51fed6d26deff00c86844cbb8231c902b0bc7165a1bd9417dacacfb627f2d0c04066cf383edc515a94651e64565798d6c3d5fabd4bb5b5b769f91cbf86a590fc37d756e0e6dd3708b7740481c0fc71f9d7015d3ebbe2982f34b8f47d26cfec7a6a1dc549cb487dff1e6b98ae4a8d3b25d0fa4c0d29c5cea4d5b99deddbd7cd851451599de145152db426e6ea18030532baa6e3d064e33409b495d9720d0b53b8d422b18ed243712a7988bc0057aeecf4c7bd7bd5924b1d85ba5c1cccb12890e73f3639aab0416fe1fd042e59a1b2b724b1fbc55464d72969f1474e9207377673c528ced58f0e1bf1e315df08c68eef73e33195b119a2bd285e31ede7fd7c88be235949aa6a7a2d85aa87ba94c800f41f2f27db83f9569fdab4ef879e1db7b6999e79a462c1507323773ec0702b85ff84dae1bc5c35b78159557cb4849fbb1fb1f5ebf9d779ae68767e38d1ed6fad27292042d039e9cf5561f51531929394a1b9b57a32a10a187c5694badbbeaedf2ff003386f12788758f1269df6a36df67d26394280a7867ed93dcfd2b92af5f8bc251c7e0e8344d46fe288998c8ceb8ebc9c2e7f9d7966a964ba7ea53db24e93c68df24a8410ebd8f158568495a523d8cb313426a5468ab28b76b5f55defdcd88bfe3c6dffeb9ad453c132a6e689c2fa9538af44f87b3d95df8721f2e2885d5b931ca768ddd720e7af4fe55d7d7bb431bcb4e292e88fcf331cb9bc5d57276f79f4eecf9f85d4f69309ada6921907478d8a9fcc55997c6fe2448fcb5d565c6319c2e7f3c66bd7f53f0ae8daaa9fb45922b9ff96910d8df98ebf8d73727c29d29e5ddf6ebc099fbbf2ff3c5764719425ad45f85cf26a603151d294bf1b1e4571753de5c34f7334934cff79e462cc7f13572d24789d2443b5d086523b11577c63a4dae89e269ec2cc3086344237b64925413cfe359f07415e829294135b33c9e5942ab8cb747a59f88f34da6f96b6612ed9769977fca0fa81fd2b948bb55087b55f87b572aa50a7f0a3d09569d5b73bb9535cfbd6bff005c8ffe846b3ad84ad7508809131702321b043678e7b735775a943dcc710393126d6f639271fad6ef833c3d69a81fed0bad462b77b7955e38d8a9dd820e5813d33c57ca62173e225cbdcfd6b2ea8b0b9553955d3dd5f8ec6f69ff0010ee74eb8fecff0011593a4d190af2a0e47b95ff000a8bc7fe19fb4a3788ac18488c8ad328eeb8e1c7e18cfe75a5af78125d77c51f6f7bb54b39157cc0a3e71818c0edcfad3bc61e20b5f0e6909a2dac62496483ca08c72123da5727deb4927cad54dba1e5d2a94fdbd29e097bf2f892dbcf7d8ea34568db42b06888319b78f6e3d368ae53e25697a8ea1636b2da446582db7bcc14f2381838efc66b0bc21e3c8b48b11a7ea4923431e7ca91064a8fee91e95d4e85e3bb6d775c3a7c56b2448c85a391db9623a823b71ef55cf0a90e56f730783c5e0b132af185d46eefd2c790c967710dac573242c90cd9f2ddb8df8eb8f5a82bd43e27e911b585b6a8ae15a122129d8839231ee2bcbeb8ea4392563ea7018b58ba2aaedb851451599da140241041c11de8a2803da3c19ac3f893c392477c03c919304a4ffcb45c75fc8d63d9fc344b5d7e39e59e3b8d3549631480ee3c700f62338fcabcef4ad4a7d235382fadce1e26ce33c30ee0fb115ebd078ffc3f7023537a617913277c67087d09c62bb29ce1512e7dd1f298dc2e2b07524f097e49ee92dbfae8cf27f10a4517882fe386348e2599822a2ed1b73c6056d687e3bbdd162b4b558237b2854abc638672493bb3d8f3557c51e2793c4122c735b5b83031093460ee61d3d7a1e0e2b9dae772e5937167bb0c3ac461e30c4c36e97bf95cf41d47e225beada6dfd95c69c512584ac2721f0d83c9e9ed8c579f514529ce53d646b86c252c3271a4ac99a7a1eb977a06a0b7768ded2467eebafa1af64d03c53a7788211f679025c0197b773f30fa7a8f715e114e8e5921916489d9245395653820fb1aba559c34e87266195d2c62e6da5dff00ccfa428af28d0be255ed98587558cddc438f3578907d7b1af42d2bc47a56b4a3ec5788d21eb131dae3f035dd0ab19ec7c862f2dc4615fbf1d3badbfaf53c87e247fc8f379fee45ff00a00ac183a0ae83e21c524de3cbc48919db647c28cff02d62c7147028334809fee46727f3e83f5afa2fac52a3422ea4ada23e369603138cc5ce3878396afd37eaf645cb7567202824fa0a927bf4b55290b079bfbc3954ff0013fa55096eddd0c6804719eaabdfea7bd57af07199aba9eed2d177ea7e8393f09c28355718f9a4ba745ebdff002f514924924e49ea4d6af86f52b6d235eb6bebb88cb0c5b8950a09ced20633ef8ac9a2bc84ecee7d8d4a6aa41c25b3563d166f8a92b5a4ab169ca970722372f951e848c75fc6b8cd675ab9d76ea3babb09e7ac62366518dd8279c7e359b4a080c091919e47ad5caaca5a3672e1f2fc361df3528d99dc783741b5d7bc39aa5bc90c3f6bc8f22564f994e3d7d33daba9f08f824787a76bdba9d66bb6528a107ca80f5ebd4d667863c7b6ed03c7a8fd96c6deda30234894fcfec07b01fad43e30f1dd9dee8df62d266767b8ff005afb4aec5eebcf73d3e99ae98ba518a9754781888e615ebce824d464f5ea969dff004f918df1075d9f50d6e4d3c1db6b68d8551fc4d8e58fe781ff00d7ae3e8a2b9272727767d2e1a8470f4a34a3d3fab851451526e145145001451450014514500145145001451450014a0952082411d08a4a280269ef2e6e799ee2597a7df727a74eb50d14536dbdc98c631568ab05145148a0a28a2800a28a2800a28a2800a28a2800a28a2800ab1a7dbaddea56b6ce4859a648c91d40240aaf524133db5c453c671246e1d4fa10722844c9371696e74fa96888d749676f6169681a7f2d675bcf35b033d5431ec33d2a9dbf8692f6484d9ea0b3c320932cb036e05002404ea4f23a54326be86e92ea2d2ed21b80e5da442ff3120e720b11deab5b6a621b68ade5b58a78a291e401d997960075520ff0d6adc6e704218950b276fb9f7eedf9753421f0c19eee7856e650b0b221cda3ef2cc09c6cea0614f2688bc32af334526a702319a48a32a8ce24f2d4331c8e9c1a61f134afe7a496703dbca91a1877c80008085e4364f53d49aac9ad3c2f0982da18d2179591016207988148e4e7a0a5ee0258b77d6df776ff003f22f2e876315a5dbdc5f008161921b85898e55f771b7f0fd294f86c6d16c254f3bed057cf01b1e5f9424cedebd3b75ed54a3d71845e4cf6704f0f951c46372c33b33b4e41073c9a957c4f76b762e16285584a640a37000797e5edeb9c6d1d739f7a77803862aeecff002ecadfadfa15f58d20e92f0033f99e6a16dac86375e7f894f23da974fd221bbb44b8b8be5b6592630443cb672cc0027a741f30a6dc6a16970939fecd8a291d30ac923b10db81dc7731ec08fc6ac596af6d67a3c303d9c5733a5d3ccbe697010154008da467953d7d2a7ddb9ab75bd925af35fcbfe18b561e1096f95d45cec956578b061254b29c7decf4fc2a1b7d12dd34f9a5bab80256b4f3d02a31f2ff00781467d73cfd2a387c472c6c92cb676f35c452bcb14afb814663b8f008079e79155db599da131ec8f9b75b7cf39c2bee07eb9a77819a8e2db777a5d7634ee7c1f343024b15cefdd2c711592231905ce077355ffe11d49a4896d3504995a630bb18997610a58f1df807a507c44d24e596d6ded5e6b849a79903b16656ce705b1d493818abb75af58d9c76eda5246675b933b9547453952bfc4e4e793d314ed033e6c5c747ab7e4adb75f9edf89566f0c08265125faa43e434eecf0b2ba2820729d79c8c545fd81129699b514164205984fe53124336d036f5ce41a8c6b71a5c3cb1e996eab2a3473219246120241e496ce723b1a65ceb725c5bcb6e96d0c303c4912a26e3b155b7704927a93d697b86b158ad137f97fc1d7f02fc5e13791a73f6a668a3d9b5a0b6794b074de0951c8183deb374db486e25be5901758ad65910f23e651c1a9e1d7fcbbb5ba92c209664589518bc8bb762851c2b0cfdd079aa506a12c135d4a1559ae6378df3d83752293e5e85423886a4a4fa2b6dbf5d8d5bef0b3da69326a0b725d6355664784a1c3100753ee2a85a6912dec568f0c8a7cfb8fb3b03ff002ccf0413ed8c9fc0d4b77aeb5d4170bf63b78e6b90ab3cea5b738041e84e072a3a0a8b4dd627d32deee1895185c26dcb754382372fbe1987e343e4bf904162553777ef5fcb6f979ea6c5be8b64ed6e1903ab9b7f9d5986e0f2b2938ed902abc7e1869ace4bafb48886d7914346c502a923e67e8092a4015521d7ee205802c511f24440673cf96e5c77f534adaef9d631dadd58c370b16ef2cb3c836ee24f40c0753e9557819fb3c545bb3ebe4f4f990d85ac33e9baa4d22e5e0851e339e84c8aa7f426b3aa782ede0b7b985402b70811892780183763eddea0acd9db0525295fbe9f720a28a291a051451400514514005145140051451400514514005145140051451400514514005145140051451400514514005145140051451401ffd9ffe101a0687474703a2f2f6e732e61646f62652e636f6d2f7861702f312e302f003c3f787061636b657420626567696e3d27efbbbf272069643d2757354d304d7043656869487a7265537a4e54637a6b633964273f3e0a3c783a786d706d65746120786d6c6e733a783d2761646f62653a6e733a6d6574612f273e0a3c7264663a52444620786d6c6e733a7264663d27687474703a2f2f7777772e77332e6f72672f313939392f30322f32322d7264662d73796e7461782d6e7323273e0a0a203c7264663a4465736372697074696f6e20786d6c6e733a786d704d4d3d27687474703a2f2f6e732e61646f62652e636f6d2f7861702f312e302f6d6d2f273e0a20203c786d704d4d3a446f63756d656e744944207264663a7265736f757263653d2761646f62653a646f6369643a70686f746f73686f703a36353837643365332d336639652d313164642d383231362d62623234326633303333383727202f3e0a203c2f7264663a4465736372697074696f6e3e0a0a3c2f7264663a5244463e0a3c2f783a786d706d6574613e0a3c3f787061636b657420656e643d2772273f3e0affdb0043000a07070b080b120a0a1216110e11161b171616171b22171717171722110c0c0c0c0c0c110c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0cffdb0043010b0e0e1f131f22181822140e0e0e14140e0e0e0e14110c0c0c0c0c11110c0c0c0c0c0c110c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0c0cffc00011080159016e03011100021101031101ffc4001b00010002030101000000000000000000000004060305070201ffc40052100002010303010503050b09060308030001020300041105122131061322415132617107144252811523346272739192a1b1b23335548293a2c1d2f0164344d3d4e32463c317367483a3b3e1f184c2d1ffc4001b01010002030101000000000000000000000003040102050607ffc4004a110001030203040801090604060104030001000211032112314104516171051322328191a1b152144262728292c1d1f0152333b2d3e193a2c2d20616435373f163344483b384a3c3ffda000c03010002110311003f00d757a75e2128895944ac225112889444a225112889444a225112889444a225112889444a2251128895944ac225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112b2895844a22f51c6f2b048c1663c0006493e800c927dc284c5cd80cc9b01cc959009b0924e405c93c00b959aeb4fb9b4c1b889e307a6e52b9f702c0027dd5ab5e1d910e8ce0831e4b77d3733bc1cc9cb102d0794a8f5b28d288a547a5de4b1f7c90c8d1e33b8292b8f33b80c103cce702b4351a0c12d07712019e52a5149c4486b8b73c41a488df3111c545add44b23dbcb1a891d1955bd924100fe4b10037d84d60106c08246601048e6330b62d22e4100e448201e44d8f82c75b2d52b089444a225112889444a225112889444a225112889444a225112889444a2251128895944ac225112889444a225112889444a225112889444a22511288944572f937811ee6795865910053e9b89dd8f4242819eb8c8e84d73b6e36034249237c011e525763a31a0971d5a000776226639c0f0b6aae577143ac417166df4498ce79dad859239173e6a244707d722b9cd26990edfda1c5b241079e120aebbc0aa1cc3a4b0cdf0ba039ae1c40735c38d971d962685da3718652411e847047d8462bd10337d0891c8dc2f2244120d8b490781163eab63d9cd37ee9ea115b9194cee7f4dabe26071f5b0107bd85435df81a4e4621bbf11b08e59f20ac6cd4bac786e6d9c4edd81b720f3eef32175b59e359be6ab8dca9bb03c973b1381c004ab0503a6c3eeae04189d0989de624fb89e6bd5e2138750dc50346ce16f99063915ca7b55125bead3ac6a02860718e3242bb71d30589247bebbbb399609b982275b1205f800bcbed6036a3a2000418d2480e36e2499e6b7bda2ed24f79a6775259bc4b26df1b03b07475ee495504b053b4e46109c66aad1a01ae90e0e2d9ecb7bc7438ae729bf18c95dda7692f64163981f1db74e01f38609024902d7169cd52802c700649ff5c015d25c6539743d458645b4c47e6dbf6786a3eb5bf133ef37f353f50ff82a7dc77e48da1ea283735b4c00f3eedbf69dbd29d6b7e267de6fe69d43fe17fdc77e4a0904707ad48a05ea389e56091a9663d001927e00649fb05098ce00de6c078959009b0924e400927c029bf707523ff0b3ff0066dfe5a8fad6fc4cfbcdfcd4df277fc153ee3bf258a7d2af6dd77cd04a8beac8c07dacca07edac8a8d3605a4ee0e04f902b5751736e5af68dee6b80f322145add44a4db6997774bbede19245f5542c3e1b9548cfbb35a39e1b996b4ee7100f912a46d273aed6b9c37b5a5c3cc02b37dc1d4bfa2cff00d9b7f96b5eb5bf133ef37f35bfc9dff054fb8efc961b9d36eed143dc43246a4e01752a09eb805c004e01381ce01ad9af0ec8b5c7386904c6fb4ad1d49cdbb9ae603605cd2d04ee9205d46add469444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225115dbe4d7f95b9fc94fdef5cddbb26f377b05dae8ccddc9bee56cb4cd4bb8ed1ddd9b1f0cdb481f8e8aac31e9ba32f9f5daa3c8542f64d36bb56483f55ce23d1d1e6558a5562b3d872a9047d76341b736e29df0156bb75a77ccf52695461271bc7a6ef6651f9458778df9c07ceae6c8fc4d8d59d9f0cdbe1161f5573b6fa785f3936a0c43762c9fe33da3f596efe4e34ddb1cb7ec3963dda7c061e423d433145cf91461eb55b6d7dc377768f3366f9093e215ce8da562f399ec3790bb88de09813bda46f52fb35a97dd2d5ef671ec00aa9e9b54b22907f1f0643e85c81c62a3aecc0c60d6ee77d670048f0cbc14db355eb2a543a00d6b7eab4900fdabbbc5547b65fcef71f15fe14ae86cddc6f8ff315c8db7f88ee63f95aacfdaffe60b6f8c5fc0d54b66fe23bedff00305d3db3f82dff00f1ff0021503e4ded239279ee1c65e3550a7d37efdc47a3623033d70587426a5db5c400347124f1c311e1798df1b941d18c04b9c6ee606869dd8e648e3d9027712355eb56ede5f5b5dcb04091048dd9064124ed2577310ea3923380a300e39eb58a7b235cd04974b8075880048981626dcfc966b74839ae2d019858e2d120927098924380bc4e56e2bce97dbebe9eea2867488a48eaa768208dc42ee525d865739c1520e31c672335363680482e9682e12410604c1100de37a52e90739c010cc2e70698041188c4825c45a66e2f95b35f3e51ed228ae219d14079158311e7b766d638ead872b93ce028e80536271208cc34823862990385a637c9d563a498016b859ce04388d70c413bcde27700345b1ec2c51dae972de85064cb927cf08032c60f92e77138ea5b9ce0621dac973c37e6c081a4b8c13cf2f2e6ac6c0036997c4ba5d275218243674199e6792d39f946d473c470e3f25bf7f7b567e44ddeff0036ff00b554fda4fdd4fc9dfef5bcec9f6b2e75ab97b6b948c0119705011d0a21560ef20208933918c63cf3c55da36714c02093270c3a0e60990401b95dd936b756716b834437102d919102087174ce2e1971550ed259c56fabcb04436c7bc703a0dc15d828e8002c7681c28c00302ba141c4b0137306e75c24813bec04ef5c9da581b50b459b88586431004c6e124c0c8642caf9da7d567d06d22f9846bb73b7904aa281e100215c13d01638e0f0490472e8531549c44cc62b101ce24df39cb92ee6d554d068c0011386e096b1a058434889c849d37aaaff00ed07541f462fd56ff9957be46cdeef31fed5cbfda353733c9dfee5add67b5177acc6b15c840aadb86d041ce0af259df8c13e439f3a9a96ce299918a488ed104467a00abd7dadd5800ec2034e2184106608d49b5d69eac2a69444a225112889444a225112889444a2251128895944ac2256512b089444a225112889444a225112889444a225115dbe4d7f95b9fc94fdef5cddbb26f377b05dae8ccddc9bee56abb4776f65afc9731fb51c88c3df8543b4fb98707dc6a7a0dc54c039383879937f0cd55da5f82b170cd8e6bb9c35a60f0391569edad9aea9a6477b072536bafbd24c29031ef68db3e414fad51d95d81c5a6d32d3c1cc93f83878ae9edcceb181e2e5b0e6ef2ca9008f569f059f5161d9dd07ba53f7c09dd8c79c8f9ef1d7d30cd24a3cf0beb5ab075b52742ec467e06e40f806b7c54950fc9e947ce0dc022d351fde23912e7f82d1fc9aff002f71f90bfbdaacedd93799f60a8f4666ee4df72b4bdb2fe77b8f8aff000a559d9bb8df1fe62a96dbfc47731fcad567ed7ff305b7c62fe06aa5b37f11df6ff982e9ed9fc16fd8fe42a37c99fb575f08ff00f5ab7dbbe6fdaff4a8fa2fe7fd8ff5aaa6bbfce373f9e7fe26abd47badfaadf60b97b477ddf5ddfcc579d1ff000eb7fcea7f12d66a775df55dfca5628f7dbf5d9fcc15b3e52fdbb5f849ff00a554361f9df67fd4babd27f33edffa54dec8ff00304dff00cdfe1151ed3fc41f63dd4db1ff0005df6ff9573aaec2f3cad7f275fce4ff00986fe286a86dbdd1f5c7f2b9757a37be7ff19fe66281db2fe779fe2bfc2952ecddc1e3fcc557db7f88efb3fcad5b1d3fe50ef2da311cf1acdb463764ab1c71976c3863ea76a93d4e49cd42fd8c1320964de231347216207895629f48b9a21c0548b6292d718f88f6813c604eb254e87e5259dd55edb0a48071273cf0480630091d7048cf4c8eb511d87e95f8b6dfcd6e7e8a71d2726edb13121d71c630df948e6163f942d26dedd63bc85423b31560a301b82e1c8181bc10416eac08ce702b3b1d4265a6e00913722f040e1c34d335af48d10d87801ae270ba2c1d6241205a44193999be4a915d35c54a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a22511288acdd8ad6ed7489266bb62a1c281804f42c4fb20e3a8eb54b6aa46a461be1266e067119f25d2d86bb691762318800201394ce5cd6afb457b15fea335cc0731b9182463a2aa9e0e08e54f5a9e8b4b5a01b1133ae649cc702ab6d2f0f79736ed7441cb268191be6159fb29dafb4b5b4165a812bdde76b10581524b0421033028490382bb7680463154b68d98b9d89b7c59890d208b489205f3ce66574b64db1ad6e0792dc3dd741702d2640384132d9816888dcb5bdb2ed2c7abba416a49823c9248c6e63c0214f882a2e42960a4977cae369336cd40d392eef3ad19e168d245a498989c85f355b6dda85586b67036e49b6271b0201bc3448130649b4415f3b15addae932ccf76c543aa81804f20927d9071c11d6b3b5522f030de099b819c6f4d86bb691717180e000804e44ce4b59da3bd8aff5196e60398dc8c1231d1554f0704720f5a9a834b5a01b1133ae649cc702ab6d2f0f79736ed7441cb268191be616f3b45da1b2bfd261b38189950c7905481e15646c31001c310383cf5155a8d1735e5c6cd38a0c839b8116cf257769da1afa6d6824bdb824104775a5a6e446656a7b31da03a1dc190a978a4003a8ebc72ae99e0b265b0090183104838613d7a3d60dce6dda4e57cc1e06d7cc4055765da3a933de6384380ced9386922f6300826e33163b9d4fb2d7d21b89d0f78dcb785c64f992226d9b8f991d4f24927354db4eab4402308caed36e1884c6e1a2e83eaecef389c0e237367b64f10c386779d77a5b6a5d96b2905c4087bc4e5787383e44094ecdc3aa93ec9c104100d1ccaae104d8d8dda2db8e1bc6f1ae48dabb3b0e200e26dc59e60ef01c62771d330abbda7ed0fddcb857552914608407af3cb3be32033617c2090a140dc7ad5ba147ab1bdcebb88cad901c049b9899c82a1b56d3d719030b1a21a0e6673718b02605812046656dfb3dda3b2b1d264b39d8895bbcc0da48f10dabe2008193efe3cea0ad45ce787012d1864c81dd326c6ead6cdb4b594cb1c4879c502091da102e042a757417216fbb1daadbe957ad3dd12a8622a0804f25a36030a09e88dcf4fd355769a65ed86dc8703981601c35e615fd8ab36938975816168804dcb9a721c0151fb417d05fea725cc4730b95e482380155b83861ec91ebe95bd1616b003670078dc9245f2d428b68a81f50b85d8e2dbc11600036cf42ac3dff00647ea37ff53fcd5522b6f1fe4fc974316cdb8fff00d9f9af4975d928d83aa1ca9c8e243c8e464124119f22083d08c560b6b1d73b7cc19f10242c87ece2f06419122a1122e2c490791b2d576bbb4e9ad32436e0886324e5b82cc78ddb413b5546428ce4ee2580e009f66a1d5c93de75a064d19c4ea4eba08b2abb66d42b406c8636f26c5ceca62f00098d4c998c956eae2e725112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a2251128895944ac225112889444a225112889444a225112889444a225112889444a225112b2895844a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444aca256112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a2251128894459ed6ce5bb2e2219d8a5cf38f0ae37119ea7240c0c9e6a1ab59b4e31480f70a6d804f6df300c642c6e6d65352a2eab3860f56d351d263b0dcc8de6e040dea60d027367f3edc9ddeddd8c9dd8ced3c6ddb91ce7c5e5554edcd153aa87e3c5831400c92245e71106d1d9d55b1b038d3eb659830e30d125e4030465841179ed68a54fd9aeeec96ee37691982b6d0be4d83818672c46e1ce067ae05566748e2a869b836935a5ed2f73ed8a9cc4c8606825bbce712acbfa370d3151a5d55ce0c7063597c3522620bc9203a66349853aefb2b14222300797322ef048f6392f8da232bd00ceecf3c62a9d2e9473b10796528a6e348b5a4fef8406666a4e66d186d7b2bb57a2dadc3803eacd46f5a1ce03f737c790a7190d715ecb43acdaada5e490c630a0f03d0101c0c9c9380d8c924f15dad8ea9ab4dae3773810e3612e638b0981004e19b002eb89b6d214aa39adb34105a2e61ae6878126498c51727250aadaa69594570b0d1a3985a5c6c8fbb11fdf01032e48c2b30da4390c41cb1c8ea39af295f6c2ceb5935319abfba70718a6d63e5cd07102c05a221a20e46cbd650d8c3fa97c53c0da5fbd69689a8e7b21ae23096bc82665c64662ea67dc487e7bdff771771ddedd9b47b79ddbf66dd9ecf1bb3bbcba555f973babc18aaf5dd663eb313bf85870e0c78b1f7af8630eb9ab5f216f598f0d2ea7abc1d5e06ff17162c7830e0eedb14e2d2217c8343852ee599e388c2c1422ed0769180c7615dabb88272bc9f3a3f6e71a6d6875515585c6a3f1118c38cb4630ec4e81a3808d119b0b4547b8b691a4f0d14d985a70168871c25b85b27569beab5ba9e90b696b77332a78990c781ca0dca1957c23664300421c119078ae86cdb61aafa2d05fd96bdb5b11386abbab2438f68e382d9978906085cfda763149959c453ed398ea3840c54998da081d9182418861822caad5e917994a22d8e816497b76b14abb930c58723a021795208f195e84550dbeb1a54cb9a70bcb9ad6980609326ce041ec07660ae86c14055a81ae18981ae739b24480205da411db73755b5b7ecbab59b4b3875986e214107a7b0bb76b96248e36b64822b9b53a4c8a81ac2c7d1381a5ee045dd18dd32c0009bcb6010574d9d163ab2e787b2b0c6e0c6106cd9c0d887c931683265457ece77761f3d772ae17250afbf6aae4b295246d272a793d2acb7a47155ea8343da5d85b51aed225ce801c1c01988709033555dd1d86975a5c58f0dc6ea6e6eb30d6c92d2d26d320c1d146bad067b5b55bc765d8c14e013bbc582010540c8cf386238e2ac52db9b51e6980e0f69737110301eae41321d3062d2dd42af576075360a84b4b086bb089c63ac881044489bf6b42a1ded94b6529866003800e01cf5e47233d47975ab546b36ab71364b4922482db8cec7df2556bd07517617c07403621c20e571ec6eb054ca04a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a22511288b6bd9b9d22bd024202ba9539e873d1493c789801ef2715cde926175325b25d4dcda830ddc30982e117ec82498c8095d4e8c786d501d01b51aea671581c570d336ed10001a9202bc471471208914041f440e3d4f87a7249278e4924f26bc63aa171c44973cdcb8925c48b0be760001b80017b26b03461003582c1a000d00dcd85ae49277932bd038e05692b74dd49595cfb5bb93737923918c1db8fc9f0024fa9db93e99c7957bbd869f5749a271626f593c6a76e07013037c4eabc1edd57acaae31870bbab0358a7d893c4c4f0cb49506aeaa2be9538ce293cb96ab31ce37e9e6ae9d9ab899edbbb9d4af7780a48c657a8e0e33b704061d4601e4127c674a31ad7e2610eeb4173c348706d49ed5c4c62b12d39198b1007b3e8c7b9d4f0bc16f544318482d2ea71d9b10270f7710cc44dc1277512b4ac110658ff00fbf3e3a572dad2e302e4e4395ce70325d5262e6c17c70d1b146e08eb58702d306c4588dcb22f7170725a0ed55c4c2110c4a4a372e40ce029564048c850586ec9c13b700e0303dce88634b8bdc407d386d26921b89d5039ae20120b886f6604818e6270c70fa59ee0c0c68259524d57005d85b4cb5cd0480434175c93138605a554769c671c57ac9f3ddaf92f251ce37e9e6be51616d7b35726def94019ef0143ee070d91f02809eb95c8eb82397d274f1d22498ea88a8371225984f30f31b9d1a485d5e8ca982a81122a834cef00f6f10e45827789d615eb7578b95ed50e1860f23fd7956418cac778b10b044f11b8de5789a18a64eee550c9e846471d0807a63c88e95bb2a169969735d9626921c67312339d46ab47d30e10e01cdcf0b802046460d8469b950b5cb85b9be96443b9490011c8214040411c1076e411c1ebe75edf61a659498d20b5d05c41b105ee2f820dc118a20dc645787dbea07d5710439b21a08320863432411620969322c73502aeaa29444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112b28ba169b3192d6263c928b9f8e0027f48af9eed4dc351e3202a3e06e18891e842fa1eccec54d8732ea6c24ef3844facadb5a49077322cb80c7a67af4e318c9e18678f5ada8b9b85c1d01c7ba4ddd7168ccd9c26dbeeb67832224819c586779e6141dd54e54eb5f7da4c5796ef1c6b1a4acd9dfb403d7792ceabbc9643839ea4f3eb5d4d9b6c349cd738bdf49adc3d5e225b0198000d71c2007091c05973369d8c556b9ad0c6547bb175984629c41e4973462248b133726ebdde6942eacd2d0b04650b96033ca8c31032a7c5cf53e7ce6b1476ceaea1a905e1c5f0c2e886bcc804c3bbb6c85e3459adb1f5b4c5390c20325e1b32580024096f7af99d57bbdd316f2d12d1d8809b7c40724a82b9c1c81bb27cce3df5a51dacd279a80038b176093003ce289104e181a09e0b7adb20aac14c920370f6c0124b061983206293be256727690b9ce140fd1b87ff009aa4f74df29738c7383e8ae31b16dc009df12b35add7cde412019c678f8f1d79f5a52a980e2ce26d966238ef5b3d988465c73c979b8b8efe4690f1bbcbf675ac54a98c97658af19c5a3f046b7088ce3ff6b04918b849202701d3191fd604807ae370fd22a4a353010fccd378741c89104031be0f91dca2ad4fac05a640a8c2c246603a41226d69f558934c58ec0d82bf0411b88e7c44b93b723eb11d47156ced78aaf5c4090e6bb00303b0d0d0314122708391555bb261a5d482630b9b8c804fef0971386403de2330bcfdcbdba79b15605b690188c753b89c0dc47048e0935b7cb26af5a410dc41e69874d9a008921a0e537016bf238a5d5020bb0960a85b1771249804919c5895f6d34e8ace1890a2195782e146efa4c4ab901f9f67e0715a6d1b53aa97905e29bcc8a65c7044b400580e0302f9662464b7d9f656d20d10c351820d40d18a60c90e23109cb3c8c299bab9f2afa9da849010820c1c03923ecc67a1cf53cf3fa6ae6d0e6f670458192db1b444e5c73505206f8a6f113e331e8b47aece62b1948ea576feb1087fbac6a4d81b8eab0681d8ff00c306a0f56855f6f7e0a4f3bdb83fc42299f471543af76bc125112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225115eb48468ace247ebb73fa72ca0fbc2b007e15f3fdb9e1d55e5bddc703504b0063883b9ce6970e057d0762616d26077783013a118a5c011bdad21a78853572c70a093e839fd8326a98939493b8093e415c36e0379b2f8490707aff00aeb583e47d7d565790fb587bf8fb7920fe8041f5e33d056c0dbeadc7224023cc823417de56a47adbc6267d08e36dcb3c122778bde7b1919f879f4e7f4566991231776462e5ae57f25870306338b73f1b2f32baef6d9ece4e3e1e5d79e9ebcd61e4498eec9c39f766d9df2df75902d7ce2fcf551d5f393ef3fb0ed1fb001f656ae3ec3d4027d493e2b668f73ef1ec00f0526cad24bc62a98181924fe8038cf27fc0d494691a8604081249c86eca4c9fc0ad5ef0ccf5b00179bbb77b493bb9319c6411d08e99e707a8239f31e9cd62ad334cc18989046441d7cc11cc6ebacb1c1e247233983fa8580be083effdfc11fb73f102b469d3783e6d05c3da3912b2e1ee3d4c1f7f3852ed5e3ef57bdf633cff00a5e7ae3a549488918bb93dacf2f0be7192d1e0c18ef6997e365e1e41b8ede9938f87975f77ad6ae37319498e536f45b01e717e7aac5bf7313e9c0ff1fb77707f240f2358274f13cce5fe5b8e677a01f97e7eb9f25e972c70064fbbfc00ac0be57274173e4164f90e365f4e54e0f07dfc7ec3436cec4660d88e60a7a8de2eb59da04696c9c279618fc01cb7e81e23ee535d4e8c786d66cfce9603b9ef0437ef1ec8e2e0b97d26c2ea2e8f9b0f237b184177dd1da3c1a552ebdcaf0a9444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a22511288959457ad3e5df6d131ea517f4e003fb6be71b50c351e34151e072c463d17d1f65762a6c3aba9b09e7844facab0767d8169079e07f8e7fc33f6559d84dddbe1be579fc169b4e9bafe765875e5db3861f49467e20919fd181f6547b688703f1344f120913e503c16fb39b723e840fc656a9db0327c8ff00f8ff001fd15441f63e317fc27980ac9fc4784dbf15e7bf5f51fa6b10771f22b36e0be77cbea2b17dc7c8a597949063af99fde4d65c0ee390f4689fd6f4047a9f72b6fa0ddc50b3995b6e40c678f5ce38f2ae86c4e0d2ec45ac9023139ad9b99892154da44c4026099804c65b82c7ae5d4734ead19dc360191d339638ce3ae0838f7d69b61c4e05a43c60025ae6b84e2718b137823cd6db3d81990714c1045a05ee16b1e418ebe63f78aa4d0672393b4fa25592470cc7b85ebbe5f515883b8f914b2fa265f51fa6b3078f925b82f48703e3cfe9f17f8d649f481f7401f82c01f89f333f8adcf67d373bb9f2000fb724ff0008fd35d1d844971dc001f68927f942a9b49c86f24f965eebc6bcc3bf503ea8cfe93fe15aeda7b43ea89e372b3b3e479fe0157f599bbbb3948f35c7eb1087f631a747b71d660dcfc7fe103507ab028ba41f828bcef660ff00108a7ece54aafa02f9f25112889444aca256112889444a225112889444a2251128895944ac225112889444a225112889444a225112889444a2251159f4cbe616b1aa819008c9f71207031e58fa5f65782e92606d67ccdc8740b77d8d713379b922308e6be87d1831d161b0805bbcf61ee6445a2cd07336d14c8754b9809685f6123070074ebf4c360f1d460fa552a750d3bb6c48893da319e47b3a6ad2ba4ea01dde9205e2604e5988779158e7bd9ee0e6591988f53fbb1803ecac3ea17ddc7111948169dc00016cda4d6e4227893ee4ad5ea5a83d9805222d9fa47d91eec8cb6e3d0676f3c8ddd2a6a2cc79ba3e8c993e06d03589dc617336eda8ecf186997c91fbd2075424c6125b2ec67218b05c823164b6367334f187643193e4dd7e3c738f4dc14f1ec818cd77b7098043c0d5b97fef91238abb46a1a8d0e2d75127e6548c438daf1bb1358eb49681132d6a35215956b0b42a3eab3cf05b97b55dcf91e592073960bf488381d0819c91806a46004dec3ca4ee95252687187186c1d624e8274d4fa2f7612cd35babdc2ed90f518c7a804a9e54918247bfa0e95ab8006d71a6beab5a8002436ed191cf4b89d60da5646ad5602c4d595b050750b96b68cba2190fa0f2f7b79edf5daa7df8eb535366230486713ec349e6478e4a0da6b1a4dc4d6bab11f35916fa4fcdd846a5ad7718175174dbd7ba4ded1943f58700fe4746fde38f6b3c54959b80c07621f092496fd6176fb1e1aa8361da0ed0dc4ea7d59193e00a7538d3987f3b39a23bf2616cedf50b9b6cf73232e7dffa320e41fb456acace6774e19cecd33e60ae83a8b5d9898e247f290bd4daa5c4edbe560ed8c64803a74e1020fd95ad4a9d619767116b58798f21e0b56d10db09033bdff23e656bf59bd2f6a5186324723f5ba797b3ea6babd10c06b0227b0c7ba0de2c19deb4f7c7cd0b8bd3230d13910f7b18343325f95f461d5572bdb2f089444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a22511288944573ecbf671f51b21389b60dcc31b3774c73bbbc4ebe9b78f5af3bb7ecec7be5c1f8b0b41c2e0d04098b163cce93390165eb3a33687b2900d2cc21ce2039a5c44904dc3d8226f11a9badc7fb1727f491fd9ffdeaa1f23a7baa7f88dfe92ea7caea6fa7fe1bbfaa9fec5c9fd247f65ff7a9f23a7baa7f88dfe927caea6fa7fe1bbfaabe8ec6c83fe247f65ff7ab1f23a7baa7f88dfe92c7caaa6fa7fe1bbfaabd0ec84a3fe247f65ff7a9f23a7baa7df6ff004963e53537d3fb8efeaaf43b2730ff00895fecbfefd3e474f754ff0011bfd258f9454df4fee3bfa8bd0ecb4e3fe257fb2ffbf4f9153dd53fc46ff4963e51537b3ee3bfa8bc5cf67ae2085e5170a76296c775d700b633df9c671d6b2362a6481152e40efb7531ff00696aeda1e0132c30098c0ebc09ff00b8b9ff00fb577a7ca3fd53fe7ae97ec6a5beafde67f4d71ff6c54dd4fc9dfef5e87692f1bca3fd53ff0032b1fb1a96fabf799fd35b0e98a9ba9fdd77fbd7afbb976df53f54ff00ccac7ec8a5beafde67f4d49fb5aa6ea5f75dfd45e86a972de69faa7fe6563f64d2df57ef33fa6b71d2d57752fbaffea2f42f6e1bcd3f54ff00ccac7ec9a5beafdf67f4d6e3a56aeea5f71ffd45e84f707e92fea9ff009958fd954bff0097efb7fa6b3fb5aaff00f17dc7ff0051640f39fa4bfaa7fe653f65d2ff00e5fbedfe9acfed5abffc5f71ff00d4587518a436bdebb02048ab80b8f684adb892cdd3bbc018fa479e39b9b1ec8ca2f25b8cb9cc2097b838068730c00d6b6e4c5e74c973fa436c7d7600fc01ada8d700c69692e2da80125cf7d809b46b9ad4576179f4a225112889444a225112889444a225112889444a225112889445f511a460880b31e0003249f2000c924f90142638019936007159026c2e4d801724ee005c95b9b6ec7ead70bb9602a3f1c853fa8ecaff00a571559db4b06a0fd505dea011eaae3762a8ebe1207d221a7c9c43bd160beece6a3600bdc40c14725861940f56688b851f94456ecaed764413b8cb493c03a09f051d4d99ecbb9a40172443801bc9692078c2d654cab297a5e9b26a770b6b132ac8c0eddd900900b95ca86c12a188c8c1c63392331d47e0188c902263312626e46a4296952350e1101c663148060491201bc03c2cadda57c9d36edfa8b8da3a2c67afaef91946d1e585058e4e194819a1536df805fe2769c80373c4db815d6a3d1bad4361935873face2040e0049de16f67ec46932a6c588a1f26566c8f7f8d9d5bfaead5546d4f1acf02041f2008f0215e76c34c88c387739a5d8879920f882aa5abf60ef6d097b4fbfc7e83871ee3193e3c74cc6493d7628e97e9ed6d777bb0ef369e474f1f32b935ba3dcdbb3f7addc2cf1cdb91fb24939e10ab5716f25b4862994a3af5561823cc641e4641047b883574381b8820e445c1d33e6b9ce6969820b5c3306c44dc483c0cac7595aa51149d3b4e9f529d6dad9773b7d8001cb3331e1547a9ea70002c403a3de1824d80f1249c801a93fdcd9494e91a870b44b8f80006649d00fec249017ad4f4d7d32736d2b233afb5b09214fd462557c407240ce323273c5629bf1891201cb15a788b9b2cd5a4699c24b4b86784921a771240beb02639ae8bd80fe6a1f9c7ff000ae4ed9dff00b23f15e87a3ff87f69df82b2d525d14a225112889444a228da8fe0b37e6dff007356cccc7d61ee168fc8fd53ec570815e8979259a3ac15b35494ad14c1674ad5481674ad4a902cc95a1595992b0b21352fc01bf3d1ff000dc56f4bbdf65dfccc50ed1dcfb6dfe5a8b5fa4e912ead29820645931901ce0b752c130ac0950324120e391901889ea5414c49923225a26374dc44e87c374d3a344d530d2d0e8901c482edf8601048d45ad7c8188f79672d94cd6f70bb6443823f68208c820820a904820820e0d6ed707091769c8feb220d88d0a8dec2c25aeb39b623d473045c11622eb0d6cb44a22cb6b6935dbf756e8d23f5c28c9c799c0c9c0f5ad5ce0db921a379b095b3185e61a0b8e70d12606b015a347ec05d5ce24be3dcc7f5472e7dd81948f239cb1661d0c7ce452a9b60166f6cef3660fc5de1038aea51e8e73aeffddb7e1105e7ddadf193a16ab5276274848fbb30ee38f68b36e3efcab2807dcaa17dd544ed4fce638002395c1f524f15d31b0d30230cf12e7623c64103c801c157752f93a94499b09018cf94870c3e0c8a55c7bf0a474e7ad5b66da3e7020ef6dc1f02641f13e0b9f57a34cf6082ddcfb11c8b4104780238e6aa5a8593585c3db3b2b346704af233d5802c149da4ed3c7b40e323937d8ec4038480eb89ce34369cf317c972aa33012d304b4c12d922750240363636cc151eb751adad9f65f54bd1ba281b69e72d8418f223bd28581fc506a076d0c6e644ee12ef3c331e2ad3364a8fb8698dee860e631913e12b25d764755b51b9e0661f8843ff007636671f12b8ac376963b500fd296fab801eab67ec751b9b491f44879f26927d169882a707823fd1047911561534a225112889445d1bb27a5c1a3e9e755ba1f7c642f93c958faa2a7e34ab8638e5b72a601073c7da2a1a8ec0dc81c31a39f9127834db8413aaf43b1d21499d63bbce6e39ccb69e60378b85cc5cc86e8b497bf2857f2b9f9b2a449e5c6e6c7e3337849f82003a73d4da6ec6d19cb8eb7c23c00bf9954aa748bc9ec86b1ba5b13bc49b793478e6b77d8ced25eeaf3490dd6d6544ddb80c1ce4285217c241058fb20823cf3c55da68369805b2093104c88899bde72d7557762da5d5490e821a2710184ccc45ad0449c864a076e7b3515ba7dd2b550832048a381cf8565503852588570382583019dc4cdb25727b0ebfc04dcdae5a4eb6b8dd046e8836fd9434758decdc0a8d1617b0781a5e0380cc90739983d81d2daeafbe76788e019f8b306445f8052ce71d30a0f0d526d9521b8757fa35a4127ce00f1dca1e8fa589f8be6d2f5738100780927900735d32b8cbd1ac734f140374aea83a6588033e40162064f90eb59009ca4f2136f05a9701990d19492009dd75ec1046474ac2d9718d6676b8be9e57392646e474c025576f5f085002fb80af40d229b01321ac6071b4ba225c605e6649f1c82f2858eaf570b60d4ab50b192435a4976168c44c019017dc049850ab7a7505401cdbb5d91cb2304106e082083c9455e83a838d3a83054a701c2438768073482d90439ae0e041c8efb2b4f62acacb52f9c595d025e4552a47040524bec7e769dc6324104301c820115576a739985cdc9a483b8970b48d6d884e6278abbb0b1b53131d9b802d22c435a65d0ed0c9698c8c64615b345ec943a2dc7ce2de466dca5183e3a12aea54a2af881400e72181246d200342aed06a08200821c0b6730083324efb6e88bcaead0d8c517626971905a43a0d89041040171860ce7336854dd5bb3d77797f706c2da4ee95c8e7804fd3646908dc1db2ea0163b597a640ae8d3ac1ad6e273711032bc0d010322058c8170571eb6cce7bdd81aec01c73b027e716971121c65c209b11c958fb25a941a5e9a22bb6292091fc241ddc10a415032a770230d8e41f4aa7b4b0bdd2ded0c2dbc88df63adb72e96c6f0c643bb2e0e74b483884182088906445d499fb6d0a1c451337e510bfbbbcfdbcfbaa21b39d481ca4fe4a73b50d013ce07fb9426eddca0e7b85c7a6e39fd3b71fdda93e4dc4f97f751fcacee1e7fd96787b7d01389e1751f8a437ec6eeb8fdbee35a9d94e841e608f6c4b71b58d411c8877be15bab1ed0d85f10b0cabbcfd16f0b7c02beddc479edddfa2a0752737306378b8f3131e30ac36b35d9113b8f64f80313e12b6551299288a3ea1f82cbf9b6fdc6b66e6398f75a3f23f54fb15c1c57a25e49668eb056cd5252b453059d2b552059d2b52a40b32568565665ac2c85eeeede4b9b4ee6152eed3460281927c371d00f41c93d00049c015b3080e936018e926c07698a3acd2e6c005ce351a001727b353ff006770ba95d9bd0ae6c75487e7f6ee01cec61ecab005d59d909520053e02c3a82430054e2bd50e61c2e1a620732d260800c1bc8b80798cd366d9dcca8dc6d745f091dd6bc090496c8b41b1237de2159b54ec5dbeab76f77712382c47857006d002e09657258904eee00040dbc66a8d3da4b061001026ee9264927422dc37de745d2abb136a38b9c5c098b360080008b837244ceeb46aaa1db482d6d2ee3b4b418ee620adf13975dcc7966d8e189e461828c6368e8eca4b8173be7b891c858c0d048223813ac9e46dcd6b5c1adb756c0d77332e1275384824f1034815da9aad66d2189d668b65249390006b9f0dea1d97657ed2eeae98c4f20bae435ad6b73739c6c00903792400092b6bd97b87b7d52dd90e3738539e855bc0e0fc558edcf46da7a815a5687b09cc16e36d8822d89a60c1077820189056fb3cd3aa05839afeadd0439a6f81e039a4b5c0df0b9a4b498702442ebecc106e62001d49e07da4f02b80bd565c06fd1798a68e65df130753e6a411f632920fe9ac9116320ee363e4560106e0870de0c8f3165eeb0b2b94f6d34a6b0d45e4ff0077393229f7939957e2b21240f2464aeeecb531340d59d93c8774f88f505797dba960793f36a4bc1e27be3c1c67910ac5d88eccc490aea374a1a47e630790abe52608c778f8ca9e76a608c3138a7b55724e01668b388cdc756fd5191de6740ba1b0eca00151d773aec06e1add1d1f11cc1d0445c98f3daeed4dfe9979f36b6da89b43038dcc739049dd9500152000b9e324f3819d9b676bdb264992226008cb2be4673f0df8db36b7d37616c34610e063138cc839d8004444719bc0d7e9df287791c805eaac9179951b5c7e32e0ec38ebb4a8ddd372f5a95fb183dd96bb404cb4f03a8e72637155e9748b81ed80e6ea40c2f1c45f098dd027785b0edc68b0dcdb0d5ed71b8005c8e8e8d809263cdd4b2f3804a13b8f814545b25520e03919c20fcd70cc72201b6845b32ac6df4039bd6b6244171193d8ec9dc5c0917ccb4dfba1502baab829444a229fa56932eaad2476fcc8885c2fd600aab229f27f102b9e0e08246454552a06413dd71c24fc320904f0b5f51c54f4689ab21bde6b7181f10040201d1d7b4d8e5217423136afd9d115bfb6610b8e877c780f1907a12f11419c7504e01ae4cf57524e41c4ce7d97c90ee50e057a08eb68c37bc581b1976e9c073781c4d2dbfb2e5c41070783febad76d798561ec5c73cf7e20858ac670f2e38252321c26e1c85790a23608255883e1c83536a20364ddd76b2720e788263290d92272227385d0d8412f8070b4c3aa4589653208139c39d85a63304e8afbdab78d34ab832f42981f947022c7bfbcda7dd8cf415cad9c12f6c6f93f5477bfcb2bbbb59029ba72c303eb1b37fcd0b5bf27b0ecd30bfd7918fe80a9fbd4d4db619772681e727f155ba39b0c9f89c4f900dfc15a6a92e9aa6fca3dacb2db43320ca46cdbf1e5b82846603a2e54a927a1651e752d2a65e7b2f346a012d2327ef6ba089191887089384c2d1fb43688fde526ed745e40a80c62a5131529cb5d06e448753330318995448f51ba8a33024b22c47aaab10a7c8e5010a73e79183563ada948fef698af1956a40175b530d371a48a739ef2a1f92ecfb483f27ae7649cf65da096b413a34b9e0969d4b5d5e0d89c808d59abb53ab02ca74ea4d405a5f50606b43aced4b729bb9cd0371c9366e8da7b2b856da2b502da0e151b4e838d47d4733b4c90435f670070329bcb8023134495f718ae8ecd47aa63586e5a0e2232c4e25e63802e81bc095c1e90dabe5355f540c2d790180e7829b5b4d85db9ce6b039c2480490090164b7b996d5c4b03b238e8ca707de3230707a11d08e0e454ee6875880e07306e3d5516b8b4cb496b8645a60f98dfa8d5754ec75edddf69e26bd3b98b10ac460b28c004e300e1f7a838190a339ea787b4b435d0db00048cc071d04f083e2bd46c6f73d92fb924e17645cd1a98819e2131703c4ecb559248ece67873de08db6ed1b8eec1d9b500258eec63823ccf19a829805c2630970c52606191327410ac552435c44e20d7618188e283860099330b9c47732dd5bc72cec5e439cb1e49c12a371eac400064e4e0019e2bace686920585a00c8489b0d2f7b2e253717341712e7199273306049cc900449bc08589eb0b62b0356c161616ad960ac0f5b051953ad7b4da9d92ec8676da3a06c381ee1de87c0f403007954668b5d9813bc767da148dda1edc8981a1877876a607252cfca1eaca31f7a3ef29cfec60bfddc569f246fd2e53fda7d564edcf1f09e3067d0c7a2d3ea3da9d4f51531dc4edb0f555c2291f55846177afb9cb0f3eb53b2835b9012353da23889983ca1577ed2f7d8930746c3411b8c448e72b522a6559668eb056cd5252b453059d2b552059d2b52a40b32568565665ac2c859e5bab8b5b42f6accb219a35057a9cacfe15c73e2200c0f68787904826b438c3a0b435c4ce562db9e5e99ad6a3cb5b2d2438bd8016f7ae1f61adcc5867964574cb76678919b3b8a8272369ce01394382873d548caf43c8ae41cf8498bcda778b1e7aaef3721bc813230998bcb4dc7237192af76e350bdb0b4492cd8a297c3b0ea320ec009076ab1ce58608608a186ec1b7b2b1ae243ae625a0e46f7e640d3289316b50dbaa398d059d905d0f70b9023b22f30099939c8689bc1e6534cf3b992562eedc9663924fa966c927e26bb404584003202c0720179a738b8c925ce3992649e64dcaf07d2aa6d940d66168ef021cd9b025ba13c5a481a4c4d9757a2b6c1b2d50f749a65aea753089735af821c06b85ed69205cb6601301335519b6b9830d4a757ac68c24b1b89af22d88924013f4710398b59752b74432abb1ecf5f67ea6a12f0caaf2c7d20e3380001ee761981d60a6f166b8120b8e79f51bab9411cd2c8e8bd03b1603cbc2ac481c71c0e9c5681f56aff098dd941ceabc00e2384b4133ac31d1f1037523a86cdb347ca2b3fa41e076767a249a60fd22d79023201d569922f808b0e8df27f6b2dbe9c5a51812c85d73d4ae110363c8318d8afa8f10e08355aa532c305c6b3f37bdda3be1125d60235b13102214edae2b00e6d36ec94c0c34e9322ec049eb1d85ac189c49930490012e332ad151ad9537e526dc1b5827f3590afeb02fff00a35d1d88dc8ded9fba63fd4b91d26decb5df0bb0f83813fe8564d0dd1f4fb7317b3dd201f600083ef04107de0d53abde74e789dea49f5175d0a04163632c0d8f06811cc6478ae5dda74b886f9edee1cb9886d463c931f3244198f2cc164c3139390464e335dad9c82d040c38aee02c31f75d03404b6cbcd6d608796b8e2c1d96937269997b249b9203a09337b495ab8a2695c47182ccc40007524f000f79240156098b9b0172740066555024c0b9260019926c00e6574fd6e36b3d0459b78a5648e150392ce76aed41d58e15987b9735c4a47154c5934175424db0b44993bb303c57a6ae3052c39bcb5949a05cb9e6043779b123805cef56d31f4b9fe6d290640aa5b1d016f10407e9610ae5b006e2c06400c7af4ea63122cd9204e640b491a499b6e8d6c3cf56a5d51c26ee00174640baf00eb0224daf20481261548a14a22b3fc9f4eb1ea7b0f5923651f11b65c7eac6c7ecaa5b6096fd5702795dbee42e97473a2a47c4d207310ef66957bb3d3e5b1bb95a220db4e7795cf31c87f946407868a6c02c37651f1b576962396e78701367b3b20e8f6680ee73721682dccc813dd6532c718834aa1c65bad3a87bc5bbdafcc8996bb2104c56fb67d92ef776a3643c7d6541f4bcda641f5fce451edf2e3c7b83dcd9b688ec3b2c98e3a6e69e1b8e991b4473b6dd8e66a33bd9d460f9dbdedfa5ab87cecc76a7169fb037b15b6a252538ef50a29fc6ca3aa93e5b82903d5b6af535636c692db5f090e3f560827c2413c24e8a9f47bc35f06d8da5adfad2081e3040e303559fe50353965bcf996488a200e3c99986ede7d76ab045cf421c8f68d6bb1d301b8b3738913a868311c24893bedb96fd23549760c98c00c68e7384e2e300c0dc716f5bbf93abb592c5edf3e28e4ce3f15c02a7e05964fd155b6d6c381d1cdcf8b73f4215de8d7cb0b7563b2fa2e008f321de4adb5417555435ad565ece6a0d2b277b67760332fa3a81149b09c83ba358d991f87ce015da4d5fa54c556c4e1ab4ac0ef613884eb671201196b32b955ea9d9df2463a35eee1ab6a34063b0e97686921d9e84415a1ed3768ac350b54b5b087bb01c39255531c32ed558cb6492de26240f0e30d9c8b7428b9a4b9c715b08125d3706493161161c74d686d5b4b1ed0d6370f6b11243591622006cdccdcdb2d66d57abab9894456aecef6266d436dc5e66280f207d371d4103fdda1fac7c4c3d95c10f546b6d41b66f69f913f35a7fd446e16073362175366d84be1cf9653cc3727bc69f55a779b9190821cba4430a408b14402a280001d001c003e02b8e4cdcdc9b927324e6bd1000081600400320058055ad4bb689a5decd6971112102942bd589018ac9b880a32dc32eec01ca935719b297b438102490e07400912233cb231cd73aaedc29b9cd70243402c2dcdc48061d3119d889cb25a0bfb986f9fbfb4521242580239dc79946173cf7a5fa120f51918ab0d696d9d12d104e903bb731f361562e0fed36435e4b8022f88ddf613f3f17e1648f40d427e5216feb787ffb852b06ab46a3c2fed2b7145c743e3d9f78598f63b512b9da99f4dc33fe5fef56bf286f1f2fd1f45b7c99dc394dff002f5502e7b35a94192d0311f8b87fd91173fb2a4159a751e3d9f780a375070d09e50efe592b4b32943b58104791e0fc083c835602aaef251dab651951e4eb5b850b9613595aa0a229ba7402e678e02db448c17775c6e214311919009c9e471e75a3cc02738131be04c29693711032c44367389313e12a7ea5a4dce953182e5707c88e55874dc8de63d41c32e70caa78a899503c48bef1a83b88fd03a2b15291a660f81f9ae1bc1fd11a80b0a56cb01674ad4a902dac9a44d1dac77c83742e3923aa904a10e3c94b03b5870780db4900c1d6024b7270cbe90cc4718cc797098d320070bb4e7bda7233c2723e705464add4616d74ed42df4d437772a58248a540ebbca5c2a75e00c16f17d138201351b985fd916969927e1c4c27d85b5c948da8298c4e921ae1840b9c659500cf2ccdf430558345ed6a6b37bf36b78cac6222eccdd776506d555c82a3710493963c80a078ab55d9fab6c9324bb0803288264937931969c74b5436c155d85a086866225d9e29020012204e7373b80bef2f2d22bd85ade75dd1b8c11fb883d430382ac30548041045566b8b4c8b11707f5a6f1a8575ec0f05aebb5c208fd6446608b8370b99f68bb1d71a56678332db7afd241e5dea8ea074ef17c3c12c23c807b347690fb1ecbf77cd77d53bfe89bee26ebce6d3b11a5da6f6e9eff9cc1f4c6a3e90b6f0db2ae55c5ce4a22b6f67fb4fa759d8adadf41bda324a90aad9c92c09ef0a9575cedcf236a8e7caa856a0e73b134c07000892d8811f36641cf993cd75b67da98c6617b711692410d6b819333da220898d6c05f45beeceea13f682f5afdd7bbb78014897af89f1bd99f80ceb1a804000289140f366ab598293708ed3de439e7e8b6708034049cf583c00bdb35435dc5e461a74c16536e72f7c627139121a05a200708d49b55515d3548f949bb02382d01e4b1908f80eed0e3d0ef9307f14e3a1ae96c4db976e01a3c6e7ca079ae2f49becd6ef25e778c230b7cf13bc960f93ad4a632c962c73105deb9fa2410ac17d15f7ee23a6e19182cd9df6d6080ec9d384fd210489e222391e0169d1b54c9666d8c6dfa2410081c1d8a48de24666749db0bb8ef35591a03b946d4c8f32a02b6df501b2a08e0e32320826ceccd2d609b132e83a02644f85f84dd52db5e1f50c5c086c8d4b4418df791c62d656cec7f64fe6005ede0fbf91e153feec1f33ff009ac383f5012bed138a1b4ed18fb2dee0ccfc647fa47a9be50babb1ec9d5f6ddfc43dd69ffa60ff00ac8cfe116ce56e9ec25babe5ba9f021833dd20e4973e179e5c7030be1890162325db6b12b5583c06c0ef3fbeed0345c31bccddc6d940917574d32e7073acca73d5b4665ee10ea8fd2c2cc6de3bc60985cebb6b3097579b1c85dabfa15430fb1b20d7636510c1c64f9931e8bcf6dc66a3b840f2689f595a2ab2a8a51166b2bb92ca74b984e1e360c3d38ea0e304ab0cab0c8ca92335abda1c08393841fcc71198e2b763cb0870ef34c8dd6d0f022c7815d7f45d660d62dc4f09e7a32f9ab79ab7a8faad8c30e463903cfd5a669983e07470de3f11a15eb68d6154621f69bab1da83f81d45f82d8544a75cdfb73d9e16128beb6188a43e203e8bfb591e8b27240fa2e180c02a076364ad886137734589f9cdca0f16e5c4722579edbf67c071b6cd79ed01f35f9c8dc1d9f074ef0156af3509af8ab5c36f7450a18fb454676876fa65727c6d9739c3310062e3581b9581331a027320693b85b70175cd7d42f8c5da2d18711ef168c838eb17b9ed19b930225f67f5a7d1aed6e07287c2ebf594f5c678dca4064391e21827696074ad4bac1191cda77387e0723c0ce6029766ae693b166d3678ded39c71198cae226095d76d6e63bb8967858346e3208f31fe047420f2a41040208ae039a5a60d88b11faf43a8b85eb1ae0e008bb5c2411a8fcf423306c6e9736b0dda777708b2275c300c33e470c08c8f23d47951ae2db825a77b4907cc239a1d6700e19c380709df0644ad7bf65b4a7e0db47f60c7ed42a454a2bbc7ce77899f7955cecb4cfcd6f808f685a5d43e4eed273bad1da13e87c6bf6062b20f7e5d87a015619b6b877807f1eebbd241f21cd53a9d1cd3dd2699dc7b6df0921c3ef1e4b2687d8482c2413ddb09dc72a318407eb10493230e36eec28ebb49c118abb597086f601ccccb8f09b40df173be241da86c0299c4e3d6387744431a77c19c4774c01ba608b5d515d450b53d62d34b4df75205f41d58fe44632c7de71b47d220548ca65f6682779c9a399361ee740a1ab59b4c4b886ee19b9dc9a2e79e43521727d7b541aadec976aa555b0141eb8501177638dc42e4819009c027193dea34f0343732333a4932638098e39db25e5b68abd6bcbb206000738680d13c4c491a4c49cd5ffb00a3ee586c73bdb9f3f2e33d71c9e2b95b677fc02eef47ff000fed3959aa92e92511288a35ee9b6d7ebb2e63571ef1c8f7ab8c3a9f7ab035bb5e5b9123965e2323e2b47303ac40773cfc0e63c0aabdefc9d5b4ac5ada568c1f261bc0f729cc6d81f8ccc7f1aad376b23301dc4764f8e63c80545fb103912de04621e1dd31cc95ac7f932b92dc5c263f24e7f40247f7aa61b60dc7cc7ebd1573d1e4fce11c8cf94fe2a4c7f26f6d6b13cd73334a51188006c5c8048ddcbbb0079c2b274e491c56876b2480006c90093da304e9901e456e3600d04b8979009000c2240d6e49f02173a15d45c55beec96932ea77f1841f7b8d83b9f20aa436dcfd6908daa072793d1588ad5de1ad3bdc30b46a4919f2199fee15bd96997b87c2d21ce3a000cc7374401cce40aeabab69506ab01b79c75f65bcd5bc9d0f911e63a30ca9c826b8ec796191e2342371fd5b30bd0d4a61e20f81d5a74238fbe45728b9b492ce77b797db4241fb33861f8ac30cbee22bb2d762008c8dff00b7864b825b849073698fefc8e617b82369582202ccc7000e492780001d493582638017274016c04db326c00cc95d4b47d3cd958c76b2e1885f1798cb659d79e194162bc8f1019c738ae454762248b49b6f816079da782edd36616869bc0bee9372388bc71550ed169034d9c1887de64c95f711edc79f4190573ced38e4a93576954c42fde19f11a1fcf8f35cfaf4f01b775d71c0ea3d6dfd969b52fc01bf3d1ff0dc55aa5defb2efe662a1b4773edb7f96a2c3d97d69746bc13c80b46ca51b1d40386dca0e012aca323232b9039c5495e9758d816703884e448910774826fbe157d96bf54ec46ed20b5d1980483237c102dba755d4f4fd4edb528fbdb5903af9e3a8f73a1c321f73019ea32306b86f61618702d3c723c8e44725e9e9d56d412d21c3866383866d3c080a51008c1e95a29553f57f93e86ea5335938877754232bef31e08283f130573c2ed1c0e853db0b4438638c9d30ee4739e79ef95c9add1c1c6587ab9cdb12de6db8c3f56e37405934ef93db1b7f15d334ede9ec2feaa1de4fc64c7bab57ed8e394307de7799b7f97c56d4fa398def4d43f71be4d33fe68e0b6e9d98d2d3a5b47f68cff166a035de7e73bc0c7b42b6365a63e6b7c44fbcad8c16f1dba08e155441d1540503cce154003279381d79a84926e6493992649e64dd4ed686d800d0320d0001c8080162d42fe1d3e06b9b838441f693d02a8e32cc78519193e6064d6cc617181727f449dc06ab5a95030173acd6e7bcee006a49b00b906b1aa49aadd3dd4bc16e83c95470883e03a9fa4c4b639af414a98600d1a66779399fd642068bc956aa6ab8b8eb90f85a3268e5aef327558edb509ed11d206d9de0dac47b457a98c37555247882ed2dd1891c565cc0e826f86e01c81df1a9dd331a5d6aca858086f67188711de2df867407588272248b2b6f60bb3c253f74ee06554e2207d470d311d3c046d8fd1c336015435436bad1d81adde781c9be399e1032242eb747ecf3fbc75c0b530778b17f81b378c9cc02aff5ca5dd5a9ed0ebf0e8b6e646c195b88d3cc9fac71c845eac78faa0ee22a7a344d431934779db86ee6741e3902aaed1b40a224dde6cc6eae3bcee68d4f80b90b91cd33cf234b21cbb92cc7d49f1313ef24935df02040b00200dc05805e4dce2e249b971249de4dc9f35e2b2b09444a22bf7646c52fac567b593b8bb858ab3280432fb68b730f844ca54e15895705490fb949ae56d2fc2e870c74de03834932d3912c75cb4ce62e20e50577b63663602d3d5d5612d739a043db98155960f106c6c6458c82ae700904604c54be392a30b9fc5562e40f8b1ff0ae718d240d26e7c48007a2ebb662f05da968204f004923cca8dac588d42ce5b63f4d081ee61e28dbfaae14fd95bd37e17077c26fc46447882428eb53c6d2df8810383b369f07007c1717af46bc725115efe4def646efad18931a80ea3ea9395703d03784e3a641206492797b6b459da9969e205c795c4f2e0bb9d18f2713736887347c24c87473b18df2752af55cc5db4a22511288abfae76c6d7479be6ccad24980582e30b9e543163ed1186c01ec9049e6add2d98d41366b7213378ce234194ef542bed8da470905ee8921b1d99ca49d48bc6e83aaa7eb77d0f69ee53e6104a6e48dbc91b7032d92bcf20120bb488aa00c83d6afd261a20e22deac5ec09326d9fe01ae25726bd41b4b8606bfad230dcb4370893245f7f78b9a00ce5686f6ca6b094c17000917a80c1b1ee2636750deaa4ee1e6055b6b83848b83912089f0700638e4a854a65870ba03866010e8e04b4900f0cc6a1749ec07f350fce3ff008571b6ceff00d91f8af47d1ffc3fb4efc1596a9ae8a5112889444a2251147d43f0597f36dfb8d6cdcc731eeb47e47ea9f62b838af44bc92eb1f2791449a4abc7edbbb6ff005dc095453ee1108d80fc627ceb8db5138af90030f2224facf92f43b0801808cc925dcc1803ee81e7c55a2aa2beb9a76d020d564d9d76a6efcac7f9361fb6babb3f747331ca7f395c6da7be793679c7e50a5761a28def999c659632573eb955661ef0ac47c189f869b49ecf0260f9120798f4526ca3b5c4364739009f23eaba0d73575557bb67b7e689f5bbc18f861f3f667156767ccf2fc42a9b57747d6fc0aa4ea5f8037e7a3fe1b8ae952ef7d977f33171b68ee7db6ff2d45abd3f4e9f5197b8b601a4c1214b2a938e4edef59031001242e4800b118048b2f786097586f82e0277e1063c75b66a8d3a46a186c176704b5a4c6718889df024c5f20b7fa06b16dd9a7912ea0945d7b2dc8c01ed0014842bbbc249dd206015948538aa95a99ad05a5bd5e62c6672b9bcc5f46c5c11215fd9eb37672439afeb7baeb88817100c44d8ccb81804182ae1a076aedb5b768a3568e451bb6b6391d0b2b2e478491b81c1f10c679c73eb6ce69dcc39a6d23439c1077de0f0e53d6d9f6b6d69025ae0261d176e5208dc4890633b4ade5565752889444a22e77f28d7b2b5d47699fbd2a07c7ab3165dc7d76aae17d37363da35d7d89a20bbe713867700018f126fbe06e5e7fa49e7106fcd0dc71bdc4912790103749dea9d5d05c85ea28da5758d3da6200f89e07ed358262fa012790b959689200cc90073360bb6da5b25a4296f1fb31a851f6719f89c64fa924d79b73b1124e6e249f1baf66c68680d1934068e4047fed2e96668c8b665593c8b02c07af855a324e3a78b19ea3146c4de48dc0e13e643bd965d31d980ed0b81701bec0b49b657cd51bb69651e9f6a9debf7d773be5a470376c5196485465608c3b26163c1c1652c41c574f657171b7629b0598d263138c02e39bdc403776e0405c4db9818d127acad51ddaa8e0316068921832a6d0e2db377904905526ba4b8a94456efb9fd95fe9537edff00a7ae0fed13f47eebbf35ecff00e5d77c353efd24fb9fd95fe9537edffa7a7ed13f47eebbf34ff975df0d4fbf496c746bcecfe8d23496b7527886195812a7cd49020539524ed2181192390483155db3ac1070dae080e046fd4d8ea2158a1d0952899687f68410e7d22d31912041917820ea742ad7a7ea96ba92196d2412283838e083e8c8c032e7a8c8191c8c8a85ae072badaad175230f0584dc4dc11bc384b4c6b04c1b1bac97975159c2d3dc36c8d4727d3e88c000924920000124f956498b9c82d194cbc86b46273ac00813ae66c200264d973ffb9fd95fe9537edffa7ab5fb44fd1fbaefcd41ff002ebbe1a9f7e927dcfecaff004a9bf6ff00d3d3f689fa3f75df9a7fcbaef86a7dfa4a558b767b4f62f6b7d71193d719e7cc0606dcab63cb20e3271d4d68fdbb1f7831d194b5d6e46642969f4154a776f5ac2738a94a0c652220f88577b4bb8af2159e06dd1b8c838c67cb386008e41ea05400cdc6456cf61612d7765cdb1163073cc48c8e8566acad1288944550ed268da2adc9b9d4a596379b918e54ed0a842ed864c1002920b679cf43561bb61a6037b302624389379370635500e89f9492e6e273ad8835cc6c5a059e26086e77bcaf5d974d0ad6e5974e999e6917187c8e078d82131c409380c57249099030ac6b4a9b5f5b00c5ae000449f19c84c732a76f44bb6597e170061a5ce731f8413f42e013124da604826f2b52ec5e9d7b3bddccd22339cb6d650b9e012032360b63272c72c49f3a999b53980018486d8170331ce46590b64a854d81951c5c71873ae4308898890309378937ce4a8435483418459e9644a99277b1dc03676ba8eec461c295ea1b19c8e7068dfdf9c448b7661961d91399277f15b3d8764029804123ac1d677a1f312006e712260c2d65cf697509b8ef768fc5017f4301bc7eb54e28b4693cefe997a2aaeaee3ac72007ae7eab5edaadef5efe5cfe5b7f9aa50c1b9be43f251758edeefbc7f359a0ed56a76fc098b0f4701bfbcc37ff007ab5341a748e523d32f45b8da1c359fad07d73f55b8b2f941c10b7b17c5a33fb7ba73fa71213e83caa176cbf09f077fb87e4a76ed9f10f16ff00b4ff00bbc1596cb5eb0be19826427ea93b5bed8df6bfdbb707c8d5475373730471891e6242badacd76441e1307c8c1f45383a9190462a352ad46b9afe9f656f224d32076460141dcc49040f026e60093ed300a3ccd4f4e939c440300824e4201de607866ab56acd683240241004cba48b5849f1c971815dd5e61597b29da99343664753240e72ca3820f03bc8c9e092a30ca480d85f12e326ad7a1d65f270b03a11b8f8e474bd8abdb2ed06958f698eb9033072c426d9660c4c0b8856ebaf942b358f36c8ef211c0601547e5b066271e8a0e7a6e1d6a8b7653a9006f17279081ea7c1749db6b62c0b8e80f640e6649f207c151e7ba92ee579e63b9dce49f8f901d0003800700000702ba01b86c2c0582e69717124dc9b9fd70c8705274ebd92c675b884e1d0f9f43e4cac38c860483820f3c10706b57b7108391fd03e0548c716904663c8ef07811657883b6f66f1ee911d5fcd400467f15b72e47e5053fbeb9e7673a411bee3cc41f495d21b5375041dc20f9191eb0abfac6b526ab206236c6becaf5ebd598f196381e5850001e64d9a74f0712733f80e0aa55ab8f834643dc9e27d3d4e4d32c2cb5089a0bf90c6a644d8410b97c4d842ceaebca6f20704900024f074a95baa2088974b7b595f09d08bf677a928ecbf280e6c388a786a1eafbc009649b3bb20bc4c0b666c0ab0e91d8fb0d3275bbb72ecea085dcc08191b4b0d889ced2541ce30c78ce0886a6d2e78c27080609c208262f1726d307c14b4b626d2762188b8481888204d8980d699891c89b2d5f68a3ecf5dde335ecec93a80ac1338e391b888a505c060ad86e028520106a366d9d50c2309833704c1398041038c6f9579fd0ceda22a617c38402d7319880261d0fbf00722d008b5d4aecb68fa4a4a6f34c92590a8284b70be2c12398a22cc00078276e412391597ed66a88ecc020c804191cc950feccf92b81389af70301ce63fb26c490c122e2d244c18983169a814c9444a228ba95ec363034d72e634e9b80c904f854a80afce4f195619ea31582e0db9c86f983c2d7bf05253a4eaa70b24bc8310402204920bbb3233bf9154abc4ece5f3f7b737b3c8f8c65b2781d147fe1f00724e0003249ea49ab0ddbf0d806346701ae17df9a8dfd02f79970aae3949a948d868370e5ac9cca8ff73fb2bfd2a6fdbff4f5b7ed13f47eebbf351ffcbaef86a7dfa4bdc567d9785d644ba9b72904707a8f10ff0087f51583d224dbb30441ecbb2363aad9bff0f3810436a4b4823b74b3170ba0c13a5c46b344772380ca7d41e54f3cf20f43cfad5606791c948e696920d9cd25ae1b9c0c11e0545d4759b3d2c29bb9026ee83924fa908819b68f36c6d04804e48ce1ce0dced3e7e42ea5a541f56cc05f86e4d8346e05ce21b2741326e4080554f569bb3bab4ff38b9bb97763000042a81f4541809032493924924f3d2a7a7b6f5620618cc921c49275371a40cb451d6e837d5389c1f310007d20d681a017d493724c9507ee7f657fa54dfb7fe9ea4fda27e8fdd77e6a0ff975df0d4fbf493ee7f657fa54dfb7fe9e9fb44fd0fbaefcd3fe5d77c353efd2551ae12fa3a511288ba4fc9e59186c5ee5baccfc7e4a6501f8ef327d801f3ab9445a779f41fde5795e95a989e1bff69b7fad53b47fcb83c65693b73da1f9e4a74f80fdea26f19face382bef488e473c33e58642a131d57cd86433e27f21efe0aff0046ec980758eefd41d81f053379facf17e0d819970551aaebb6944596d6d65bb9560814bc8e7000ff005c00396278500924004d6409b0b92b47bc30173886b5a2493a7e64e400b93005d763d1ec4e9f6715a93931a8048e99eae57383b77138c8071d40e95d1688006ef7d5786af53ac7b9f907b8900e61b93678c013c54dad940aa1dbcd765b18e3b3b672924996665386083c2a030c32f78d9f1290711919c31aaf59f161626e6338feff0082ed7466cc2a12f700e633b2d6b84b5cf3724836381b162089703a295d89d74ea56bdc4edbae21e093d590fb12127da23d873c9c8567e5c13b527c88398f51a1fc0ff751748ecdd53b134452a9700775af1de601a03de68ca090db3606df51b1b4d5a27b39f0d8c6403e24273b1c63251b0495c8c30c821949064700eb1bfb83a1e0551a551d4487b65a4cc123b0f683da699b39b31306418208700455ed3b01259dfc5711cc1a18dc3f2087f0f88261728c0900336e4f093e1f2300a30419b033b8db4dde36e4baf53a503d8e6969151ed2c9041a7da105d787348049021d703b5bb7dda2b1b5d5624b0b89844ecc190646e240650046c4175219b81f480c1c8a95e03ac4c1cc6f3a65ae6b9db25575226a35a6a35ad2d798385a090ebb8021a6c2e749d0aa7df694da484b5671260310c38c82cd8ca9ced60410cbb980231b8d5ed91b85a467da37f01ebc1737a56bf5d503c02c9a6d05a6f044cc1b4832083024681407abcb8c5606ad82c2c2d5b2c15824ad828cac0f5b28ca8f21e6b70a172c26b2b5414459a3ac15b35494ad14c1674ad5481674ad4a902cc95a1595996b0b216cec7443ad42d6e1c460488c491938db3a6d45caee72ce300b00064f38da79db633161195c9f4d38aef744ed3d439ee82f2581a003024b83a5c6f0d01a72064c0b4c8b8767eded6c6dbe656b309bba2771c824162588655242739007b8f56dc6aa300020198cf99e592b7b53dd51d8ded34fac030882d05ad01a082eef5a093c741015653e4e9e5b9792e260212c480a32e412480cce02a360f2409727f4d43d4dee6dc333ce72f55d53d2d0d01adfde06804b8814da40838436ee1b812c80ae1656f6d608b676fb5028caa67923e93e092ed963e2739c93c9cd58000b0b70d79ef3cd712a3dd5097ba5c49873e3b20c59b23b22c2cd116160ab7dbbd7daca25b2b672b349cb15386541d0065c1569187041cec56e06e06a1acf8b0b13731981fdfd9757a3765eb097b862a6cb343802d7d43bc19043067223116ee2148ec3eb726a56ad0dc36e9a138c93e2643ca3313cb302190b7980a5bc44939a4f917b91e641ca7d945d23b30a4e05a30d3a8240166b5edb39addc088701bc902c02b354eb94b57da4d31f54d3e4b68ce1ce0ae7a12a438527cb700573d149c9e0568f6e211ae9cc2b7b256149e1c7ba243a330d702d246fc338a35885c86589e1731c80aba9c1078208ea083c822b9fe8bdb35c1c24439ae1208b820e441d42f1585b25115e3b07da1d8469739e092623e8797788fb8f2e9c7b45949e540b545ff34fd9fc47e23ff4bcff0049ecbff55ba40aa378b35af1c459aee10747139fe522c77450de0fa2c636feb78d0fc14a383ebbc7a566b8c8f879dc7b1f351f445482e67c40541cda70b87887363ea9dea815517a44a225112889444a22eb3d8b20e91063f1bf89f357e977478fb95e33a43f8aefb3e581ab94cbbf7b77bede4eef8fd2cfbf7673547df5e6bd8b62047760618cb0c7663c2178ac2d958fb33d929758fbfcc4c76c0f5fa4f8e1962ce40008c3484100f8543306db353a78af937d4f2fcd72b6cdbc51ecb61f58e87bb4c1c8be2e49cc301062e48044f45d3b47b3d3176dac6a9c60b7563e67748d976c9e704e0740000055c6b40cadefe2735e62ad77d532f25da8193069d960868b6e13bc92b3ddddc567135c4edb6341924f9790e064924900000924800126b24c5cd8051b185e435a313dc61a06a73d6000002493600126ca9ba97ca320052c2224f93c9c0fca112125b3f4773a11d597aad57757dc3c4fe43f35dca3d1273a8e0066594ee4f02f7401c61aedc0eaa9177772decad7170c5e473924fec000c00a070aa000a0000002aa933737257a0a74c5301ad185adb003dc937249b92649372564d37529b4db85bab73875fd047d2470319561d46411c104300465ae832331fa85ad6a22ab4b1d76bbcda464e69d08d3c41904852d35d960d4db538720b3962a4f556396818e30c817c0a7191b55c056031b63be2de66381d3968a03b28753149d0435a1a1c064f6881500cc389ed113792d248267aadbea96d3daade870b0b2eedcc4003c8abb13b5595b2ac09e181157838113a6f3a73f65e3df45cd71a704d4070e1682e2edc5a00921c3b40c5c195aced07672d35a85a7503bfd9e0901e0e32c8a403b1918923382406ca915a3d81d7d62c7db810ad6cbb5ba810dff00a58bf794dc32986b889189ae0065304882155bb3fa25c6a5671b43b4202c0b31c0ce49c6143313820fb38f7d58d92a06b6f3388900721c8287a6a917d6b44756c127c77495bd87b10a4e6798fc1463fbee5bf82ac9da370f333e823dd71c6cbbcf8011ea67d9493d89b0231ba4fd61fe4c7ecad3e50efa3e47f35bfc95bf4bcc7e4a0dd76011b9b7988f738cff007d0a63f50d48ddab7807918f433eea376c9b891c1c27d447b2af6a5d91d4acf2dddf7a83ce3f17ff004f0b2e479e108f79156595da75c2773adeb97aaa753667374c437b6ffe5b3bd1579c11907ad5a0a9151a4eb5b850b96200b1c0e4d656a17c14459a3ac15b35494ad14c1674ad5481674ad4a902cc95a1595996b0b217dbe8ccb67ddafb4d3c407c4adc01fb4d72f6fc9bf58fb2f51d0070bea139368c9e41ed27d15fb49d0ec74087bc1856db8925638cf42c4ef3b234dc321460018c92724d76b0379c5c9fd400b7afb4bf683064899a749a270cd801846273a2c499933000b093a9eaf069f66d7ac4320195c1cef27f935423208738e46405cb7406b673a04e9a71dd1cd454681a8f14c487130e91058077cb81b8c23437261b995cb6d75d9e2d446a7292f282c7d01c86454f7463701b474418183835403ef8b33fab72fc17af7eca0d3ea9bd861c2273230b838bb8bcc1327371936b28379772deccd713b6e91ce49fdc00f255185551c2a800702b5266e732ac53a62980d6886b4401ee4ef24dc9cc92495ef4fd427d3a61716cdb5c7e823cd1d4f0ca7cc1f30186180232d745c58feb35ad5a4daa30b86269f0208c9cd22e08de349064120de34ef945864c25f4450f42e9e25f7b14389157dca656f8d596d7df6e22e39c663d579fadd12e126990f1986bfb2f8dc1c3b0e3c4e01c95c60992e2359622191c06523a1079523e208356019e45711cd2d241b39a4b5c0e61c0c1079150754d06cb54522e6305bc9c70e3c8624186207d56254f9a9ad5cc0ecfcf23e7fa0a7a3b4be9770903e03da619ce586d7de21db885ce3b49d979b447de0f796ec70afe60f2424a070ad81c30f0bf2460e54537d3c3c4687f03c7dd7a9d936d15c4772ab44b99a11ab984dcb67306edd64438e8aa25d153347dff003eb7eefdaefa3c7c772e3ecf5f2c673c56cdcc731eea0da2303e7bbd5be79613fa1c5742f9412069801f39571fa1cfee06add6cbc47e2bccf45ff13931d3e6d1ee42e635497ad4a225112889444a22be7c9deacbb64d3a43e2cef8f3e63812a2fbd701c2f982edf44d5aa2ed3c47e23f1f35e73a5a85c54194756f8d0832c71e725b3c1a350b5bdafecc5c5adcbde5ba17824258ed1928c7c4e1c0e4216cb2be36807693900b69529c191706fc8eb3c372b5b06d8d73431c432a30068c4605468b34b49b6202016cc923101131a4d0f4c6d5af23b45e8c72c7d1073237c71e15f22e541c039a8d8dc463cf80d7f5bd74369add4b0bf5021a3e27bacd1ca6e7e88257628204b78d61886d4400281e407000f3e07af27cf9ae8011c82f0ee717124ddce24b89cc9372564acad546d42c63d42dded66cec9060e3a8f3565ce46e560186411903208e2b0e122342a5a550d3707b7bcc322723a10620c10483041839aacdbfc9cd92366696471e830a0fb9880cd8fc92a7d08a80501bc9f20baaee9679c83187799791c40903cc10b7b69d9dd36d3f92b78f23cc8dcdf63c9bd87d86a50c0341ee7ccc95cf7ed551f9bde46e07037eeb30b7d167bad2ad2f10c53c48ca7f14647bd5800ca7d0a9047ad64b41cc051b2b39865ae734f0260f306c4702085cc7b53d9d6d1271b096824c9427a8c7b51b9e06e5c820fd2520f50c052a8cc3c8e5f915eb362dafaf6ded55901e06441c9edcec62e3e69b6444e97bd7dbddee3b339db9e33d376de9bb1c6719c7151abf844cc0c5962818a374e71c26165b7bdb8b620c323a639e1881e9ca83839190720e4120f1406382d5f4dafef06ba6dda0098cec4dc5ee20d8df35d27b01fcd43f38ff00e15768e5e25795e94fe2fd86fe2acb532e5251128894450ef348b2bee6e61473ea546ef80718703dc1ab76bcb72247006de597a28dd4daecc077122fe79faad71ec5e8e4e4db8fd66fddbea5f943f7fa0fc943f24a7f08f377e6b3c9a35969d6937cd6148cf74fc81e23e16e0c872e47b8b115a8a85c4492eed0b136cc6997a2d8d26b1a7080dec9b817c8e6733e6b8a8aef2f2eb347582b66a9295a2982b1f646c21d4ae25b5b81946889c8eaac0a85911bc880ec3072ad9c30238aab5dc5a011621d1c0820c823c072d15dd9981e4b4e45b3c41040041e127819b85e358d126d225d9278a36cec71d08f43f55c0f69727d4120e6b34ea078dc466351f98dc566a5234cc1b83dd7687f22351e4a1a56c544b32d616426a0c52c7729c113c641f4216e0823e06b97b7e4de67d97a9ff87c4bde0dc1a5046f05ed90b4f71773dc92d33b39249f112793c9201240c9e7802b904cf15ed194dacee86b6001d90058580245cc0deb1995ca842c4a8e40cf009ea42f404f99039a2db08998188d898188819027331a4ab17647b33f7624335c645b467071c176e1bbb0472aa01064230d8601482772cb4e9e2cfba3d4eefcd7336fdb3a9185bfc678913714d996323524d9a2e2412eb087748834db5b741145122a0f20a3f6f1c93e64e493c924d5c0d0340bcb3aab9c64b9ce71d4b893e17b0dc05868a25df6674cbbfe52dd013e6a361f8968b6127e39ac1a60e83c2deca666d951993dd1b9c71b7eebf101e10b472fc9c59b36639a455f4383f606dab8f7121bdf9a8ba81bcfa2e837a59f176b09de3137c4893e30470856bb6b74b5892088612350aa3dc005193e670393d49e6a7022da0b2e3bde5e4b8ddcf25ce3bcb8c9b697361a64b2d6568b05f59457d03db4e328e307fc187a329c329ea1803e558226c722a4a750d321cdb398641f7077822411a8242e35a8d8be9f7525a49edc6d8f88ea8c075c3a95603d185739c20c6efd0f3175eea8d4151a1e3baf13f54e4e69394b4820f256aec576667f9c2ea17485238f25030c1663c06da790880960c40dcdb4ae4026a7a54ef26c0653a9fc87bae3748ed8dc269b087b9f01ee6996b1a0c96c8b173a0020130266e42f9f287ab2cf3258447222f13e3eb9e114fbe342c4e38fbe60f2080ace9b6eb9e7a790f759e8aa1841a86c6a76593f00bb88e0e7401f527232a9b5597752889444a22511288bdc52bc2e248c957539041c107c88239047bab2b5734384101cd3620dc107420e6ae1a6fca2cf1284be8c4b8fa6a76b7c59305189f553181e9561b5b7df88b1fcbd970eb744837a6707d078c6dfb2eb3801c719e2ad9a0ebd6dad2bc96c8c85080db800493920828cd91c11938e6a763c3b2b46731f84ae36d3b33a8101c43b1025b8492001623b41b19e4256daa454d288b9feb7db7d46d6ee4b68d12311b15e46e271d18b12a30e30e00518560324f3551d548316116de7f473c97a4d9ba3a9bd81c4b9e5ed0eb10c6827368104cb4cb492e324130325b3ecd76d9751905adea88e66e1587b2c7c908249490fd1e4ab9e01562aadbb2acd8d8e8743c381f75536ce8eea863612fa62ee69efb06ae9101cd1ad8168b9900b85b6ac2e3251156fb7b1a3694ccdd55d0afc73b0e3dfb19feccd435b2e4447b7b4aea746122a88c9cd707728c5fccd6f8ae5d5457af4a22e9fd80fe6a1f9c7ff000abd472f12bc8f4a7f17ec37f15beb8d42dadbf969510fa1600fd8b9dc7ec1560349c813c85bcd71dcf0dcc81cc807cb3510f6934d071df2fe83fbf6e2b7ea9db8fa2d3af6ef1ebf92936da9da5d1c412a39f40c33fab9ddfb2b52c233047120c79e4b76bc3b220f0044f966a5568b74a2251146d4bf049bf36ffc2d5bb331f587b85a3f23f54fb15c2057a15e49668eb056cd5252b45305d47b25a07dcbb7ef661ff889402df8abd5621efe7327ab60721013c8af571981dd6e5c4eaefcb8735ddd9a8e0127beeb9fa2346fe7c7905b3d634f5d46d5e06c648ca9f461ca37a8e786c7254b0f3a8a9bb0907cf88398fd6b0a7a8cc608f2e0e191fcf84ae5ebc5754ae22ccb58590be6a5f801fcf47fc3735cbdbf26f33ecbd4ff00c3dfc47ffe2ff5b568ab8ebdc251175bec84691e9306cf3524fc4962d9f520f1f0007957429e43f5aaf15b7126abe74200e0d0d004708bf8cadd548a8a51152fb43dbcf9aca6db4f0aeca70d2372a08e0ac6a0aee20f05c9db9042ab03b8567d68b0bc664e5e1f9aeeecbd198c62a84b5ae12d636ce20e4e7933841170d02608248c962ecdf6caff0050bc4b49911d5f39206d2a002c5cf2cac0007c3b4162400d9e0e1954931633e11c56fb6747b29b0bda5cd2d886ba1ed712603720e04ce724000c8dd79ab4bcfa5116975eed25ae88e8274677704aed038c601c97652b92dc601ce0d44f786e7727747e2afecdb23abce12d6b5a407622e125d3101a0831179234553d57e506e6e14c7668215231b89dcff15e024671c74723aab29c620756272ecf1ccfe43d5766874535b779eb48be1030d3fb572e779b468410aa2cc5c96624927249e492792493c924f249e49aaebb40458580b002c0016000d00192f94594a225112889444a22511288ad3d80bf6b7d43e6df4275208fc640d2a30f8289171e7bbdc2a7a260c68ef7171f8ae3f4a520ea78fe75220cfd1a8431c3ef161e11c4ae995757944a22e69f2876cb16a2b2a8c192304fbca968f3fa8a83ecaa5585f98f516f685eaba29f34c8f81e40e0d700e8fbc5c7c4aaf69b6b2dddd47043edb3003dde65c9f20801727c8026a268930333fa9f0cd74eb3c31ae73bbad69278da037897121a06a4c2ed838ae92f04bcbc8b1a97721547249e001e64938000f53459026c24936005c93b801725735ed97699755716b6a736f19c96faedd030079d88090bf5892d8c04354aad4c561dd1ea7f21a2f55d1fb19a431bed55e2037fedb3320e989c409f8400264b82ab540bb0944562b0b9952c23891d8212c4804804e48cb01807800739aed6c4d1866c4e2224dce432dcbc0f4eb88ad1240ead960601cf358dfad74979c2b0356c161616ad960a9967da2d434ffe4656dbf55bc4bf00af9da0f9ec2a7df9ad1d49aecc5f78ec9f319f8cad9b5dccc898dceed0f2397842b15a7ca3c7d2f2061ef8c83cf9fdee428547ff00318fc6aabb643a10783adea267c82b6ddb87ce04716906fc9d863cca9dff00b42d2b192641eedbff00f8c47eda8fe4aee1e7fd949f2e67d2f2fef0b4fac7ca4c52c4f059424ef52bba438c641524471962dc138cc8a7d454f4f6422ee2041061b7caf9988f22ab55dbc104341320897da26ddd133f782e7e2ba4b8eb347582b66a9519da41f4a8ca9c2ed161791df5ba5cc2728e011eef553e8ca72ac3c8835c27370920e62dfdf91cc2f48c70700464448fc8f106c78af579751d9c2f3ca70a8327fc147ab31c2a8f3240a35b260666dfae0332b2e761127217fd71390e2b9483b8e7d4d75d709655ac2c85f352fc00fe7a3fe1b9ae5edf93799f65ea7fe1efe23fff0017fadab455c75ee1288ae5d8aed42590fb9f78db622731b9e8a4f58dfc9636625831c0462c5bc2d95b34aa458e5a1d070e03f195c2e91d88bff78c12f022a3066f0d167377b80105b9b80117107a1021864720ff00a041f306adaf32bc5cc6d2c4f1a1dacca403e84820371cf04e6b056cd304137008246f00c91e22cb88cd03dbc8d0ca36ba12ac3d08e08fb08ea383d4573488e617bf6b838070bb5c039a77837055c7e4ded55a79ee4f545551fd72598fc7ef4a07b89ab1405c9dc23cff00f4b87d2ef80d6e8e7179fb0006ff0039f20ba0d5b5e69288b9476cf506bdd4e45fa30fded7fabcb93ef3216fea851e59342a993cade59facaf63d1d4b05307e754fde3bed7740e01a078cad0d44ba49444a225112889444a2251165b6b692ea45862197638032064f9005caae4f403392781cd6409e67f5aad1ef0c05ceb35b7260ba06f8682606a62cbdded8cf612773728637c6707d39008232186411952464119e2b2446762b5a755b5062610f6e52343b883706e2c40370addd92ec95e45730ea371848d4160b9f19dc0aa6540c20c38620b6e03c25412713d3a66413619c6b7f65c4dbb6e639aea4d97b890d2e8ec0c2e05d07375db00811a824677fab6bce251173af942633ea10c118dcfdd8181c9cb330550073938181d791ea2a9d6b90358f72bd3f457669b9c6cdc64926c006341267709327870566ecbf6623d1a21248035d38f137d5079eea3f451c6e61cc8c327c215567a74f0fd6399ddc07eaeb93b66d86b981228b4f61b96222d8dfc4e80d9a2d9c931fb53daf1a3b8b5b750f39009cfb280fb3b8290ccec390b95c290c490403ad4a986c2e7d07f7e0a5d8b60eb862712ca40c08ef3c8ce09901a32260c9040162450efb59d4358711cf2349b880a8385c9e115624c2b36480a482c4f993cd552e2ecefc34f20bd153d9e9d012d0d66104baa3aee0d025c4bdd2408126086f00179d4f44bbd2827ced42193240dc18f18cee085b1d7839209c8ce451cc2dced3c8fb2da8ed2dad380970644bb096b6f3105c04e5944e5bd6beb4565288b7d65f8247fd6fe26aee6c3dd3f58fb05e03a77f8dffe367e28f5d05e74ac0d5b058585ab6582b0495b0519581ab651951e4eb5b850b9613595aa0a22cd1d60ad9aa4a568a60b6ba5eb379a6e7e6b21553c95e0a9f2c94704038006e186c00338a85f4c3b31277e47cc7b1b2b34eab999181bb31e4644f11759efb59bcd4b02e642ca390bc0507a676a0009c12371cb60919c56ada61b9083bf33e67d96eeaa5f9991bb21e435e25474ac95a2ccb58590be6a5f801fcf47fc3735cbdbf26f33ecbd4ffc3dfc47ff00e2ff005b568ab8ebdc25116c74fd06f35389e6b440e2338601806fad908c41208ce31cb1042824115bb584e578cf7f92ab576a65221af2585e25a4b5c599c5dc0100839ee04130085ef4ded0ea1a590b048420fa0de25f52bb1bd8e739d851b39e7ad1af2dcbc8dc7969e10b5adb253ad770188ffd46f65fc0e21deb658838705d03b2fdaa4d6c3452284b8419207b2c3a174cf236920329271b9486604e2dd3a98b811e446f0bcd6dbb11a1041c749c60136735d9e17458c892d22260c8117c3daeecb26a719bbb618ba41e5fef00fa0de5de00311bf9f11b787694c54a78ae3bc3fcc3773dc7c3949b0eda691c0ebd1718bffd224f786b8493db6f370bc876abe4da650f7301e1884603dc37ab1c7e297407f2856940e7e07ca7f30ae74bb6cc77cded349d01384b7cc35d1c95f2ad2f3a9445cdbb53d93bc8669f508f6bc2cc5ce0f89431dcd9423055093cab31da371039c53a94c8939899e226f970e0bd4ec5b73086d332ca81a298912c7168810e191701910049804da6bb61a65d6a2e63b48da461c9c741e9b998855cf9648ce38cd421a4e575d4ab59b484bc860361399e404b8c6b00c6ab04b1344e637c6e53838208cf980ca4a9c7b89158fd7ea148d762123237120b4c1e0e008f10178ac2d92889444a22511288bdc313cd22c7102cec40503a9278007bf3e7e5d6b23d4e4b573834126035a097139068cc9f0f35d8ce950dca42d7d1a4b344a3c4467c5801c8cf50586e018119c36370047470cc4c1235e3aaf0dd716970a65d4e9d427b20c7627b20c6440b48bc4898254fad9574a22c37777159c2d713b6d8d0649ff01eac4e02a8e598851c9158262e720b7630bc86b462738c00379f603327200126c1734d1f505d4bb429797380aeec403d1708e20524e07836c601e01600e066a934cba4ea6794031e565eab68a5d56ce58db96b5a0902eec4f69aa40b9ed4bad7ec985d3e2916545910e5580208f3079523dc410455efc5793220c1b16920839822c41e20d9732edb691756f7b25e3a9682520871c81c05eedfea11b70b9e186369272a29556906743afe0777e2bd67476d0d7303018a94c10586c5c249c4df8819931769991104c3ec780757b7c8cf88ff000b907ec3cfbb15ad3ef0fd6854fb7ff09fa587f3b6478e4b7ff28902cf3c02005e70adb828dc42e418cb2ae48cb193071c807d054b585c45cea05eda7e2b9bd14ec21d8886d396e12e21a0be087804c0300324696dea8eca5490c3047507f6820f422aaaf400cf107222e085f28b2afdd8ed16db51b059662c4a3b2ed0703a87e703764871d180c574b67a85ad811de2673390f0f45e33a5e88755933dc6c0c8117befce467a2b55be8d656e311c29f12371fd77dcdf6671531a84e64fb0f210172c5268c80f1127ccc9527b88f18dab8f80fdd8ad256f03828973a1d85d0c4b0a1f781b4febc7b5ff00bd5b8a8e1913e723c8c8f451ba935d981ce20f9883eab43a87602da61bad24689bd1bc4bee00f122fbc967f70ab0cda88cc07711d93f91f20aabf6307ba4b4ee3da1f811e65552fbb21aa5ae730991479c7e3cfbc22fdf71f18c1f755c6d769d70f07767d7bbeab9efd99edd310deded4f877bd169e4d2af4360c12e7d36367f46dcd580f1bdbe63f3551d49df0bbee99f652edbb1faadc2194c2628d4124c9e0e002c7ef6df7d39038c2609c72073519da1a2d388980037b59db3eefaa95bb2bcde30000925fd9c84f77bde8b482ac2a8b347582b66a9295a2982ce95aa902ce95a95205992b42b2b32d61642f9a9902c48f33321fd0b7009fb0b2e7e22b97b7e4de67d97aaff0087876de74eac09e25e081e201f25a2ae3af6ebd22348c1501663d001927dc00c927e15958262e600199360399360ba07c9dc490c73ab82b70586e56e0ec03ef676360e37bc809c75c03e556a8eba1dc738d2dce5799e9571716910ea41a70b9b0e6f584f6c6212270b58409de46aa99ae80351b90063efcfc7f59bff00dfeee2abbf33ccfbaef6cbfc366bfbb65fec8561ec1691746ec5f952b0056193c6f27c21501e5941f117036e576e4b640968b4cce42fe3c9733a52bb70f5721d54969205fab00ccb8e409160dce1d3119f429a78e04324ac1507527803c8127c8648e7a0f3ab64c700bcd35a5c6002e71c80b93ad86b65cc06a89a3ebf2dc45cc42575603eab1225da07d47cba28e09451d2a962c2e274920f239f91cb92f59d49ad41ad3fc4c0d7309f89a3b133f137b24e70e2735d3e1952645963219180208e841e4107d08abc0cf22bc939a5a60c8734c106c4116208de17ba2c2f8406183c83fe8823cc1a22d5dd6911dbd8dcc5a6c6b1492a36368c6588200c8f67ae13042a1395039a8cb6018b120e5bff00596815b6572e7b0d526a329b9b388e2860209b6b94bb32e02092b9032952558608e083d411c1041e4107820f4ae7af6e0cdc5c1b822e083910750745f28b29444a2251128894456df93a8a27bf919f99163ca7bb242c8c3d1b6b05fc9761e66ac51cf8816fc5717a5890c00775cf87f1805cd07848279b42e91571796541f945d4a5134562a484dbde363e91259101f511f76c47965b3d5462a56769a449e3a0f285e8fa26888754305d8bab6cdf080039d1bb16300eb02322674fa6f6d352b01b0b899074127888fc9901593dd8666007402b46d52388e37f5cd5eadd1d4ea5e0d276a69c341e6c20b7ee869de4a89ac768af7583ff00897c460e422f080f4076e4966ebe276623276e01c56ae7976796e197f7f1536cfb2328f744b8d8d47769e46e9b068cacd00181326eb5751ab8af1d86ed2c9bd74bb8e5483dd379823c5dd367aa6d0ddd9eaa709ca95d96a93fe69f03bb872dde5cbcf7496c620d56d8c8eb5ba19b758373a48c5a11dab10715ea58926431c803230c1079041e0820f041156bd979e0483224381904588232208c885add33b3361a5cad3db261dba124b6d07aac7bb3b41f33cb11c16c7151b6986dc67e71c95badb5bea80d7196b6f0006e223273e33234c80ce26ea5c9a95a45298249635947552c037a8ca9208c8e467cb9e95b6219489dd2254028b88c41af733e20d716ee370233b73b2e77db592caf271796522b1c98e403a965e5251d3bc565ca778b943dda80c6aa5582645f43cc6478ce53c17a7e8e0f60c0f0e688eb2913961718730fc2e061d80c3bb6640558a8175d5c3e4ff595b69dac2538498e509fae382b9e9f7d5000ce3c48aa397ab145d16df973feff008715c3e94d9f101505cd3ecbc0ff00b66e1df61d33c1c49b35745ab8bcc25112889444a2251146d4bf049bf36ffc2d5bb331f587b85a3f23f54fb15c2057a15e49668eb056cd5252b453059d2b552059d2b52a40b3256856566419e056164289acce0b2db2e0f779dc473976c6f507a6230ab1f191bc48caccac0d79fdaaae375aed6764713f38f89f40be89d0fb21a14f13ad52bc3dc0e6d60fe1b4f1825c7762889056b2a9aeeab876165b1b2637177222cd2929183f44280d23337b31890b2a2162a58ab2a93922ac52205cc026c38019cee9c8725c2e930f7f6581cea74c0a9508c9c5c70b0019bcb00739c1a0c6204c44abe43a8dadc486286547900c95560481d0921493804807d0902ad0703a8277032bcf3a939a25cd735a6c1ce696b49ce012009807c941beecbe9d7f722ee78f320eb82406c602995460315000f2dc3c2fb94003534c13273f43cff5cd58a7b654a6dc0d30c395817326e7013dd999d60ddb0492b6caa140551803800741e8001c003c80a9152f526e49b924ea5739edaf695eee57d3a0388636c39f3765ea0ffe5c6e30063c4ebbb9016a9d57cd86433e247e00fadd7a8e8ed8c300aaebd478960d18c7647eb39bae8d319caa8d575db5b8d1bb517da40d90b068bea3f2a33c92982ac873938560a4925949e6a46d42dcb2dc72feca8ed1b132b5dc0b5ff1b2ce2068e905aeb5a4890000080b26a9dafd47520519fbb8cf0563f083ebb9b2d236470417da47d1eb597542780dc2dfdfd56b4760a74af18dc2e1d53b44726c0608d0e1c437ab27c9d6a524b1cb65212563c3213ce03643a73d17700ca079b3d4d45d98dd71e398fd715cae96a21a5af100d496bc0b496c16bb8982413c1aaeb56570572eedec50c7aa1ee700b22b3e3eb9dc0923c898c46c7d49ddd49aa35b3f004f3ffd42f5dd18e2695f26bdcd64fc0003e41e5e06e88c8055ba857552889444a22511288a6e93aa4ba55ca5d43d57a8f2653c321ebc11d0e0ed6018722b66bb099ddea350abd7a22b34b0d81c88cdae176b872398d448d575dd33528753b75b9b73956ea3cd4fd2471e4ca78f4230ca4a904f41aec4247feb815e2ab51349c58eb39bae8e69c9cd3a823cae0c1042aef6e7b3f36a31a5ddaaee92204328eaca7c40a0ea5a36dc7601960e71c801a1aac9b8b9198d48fedbb8ae9f46ed42912c79c2ca8416b8f75af1638b70708ed640b44d8c8e71b4e76e39e98f3cf4c63ae73e554d7a99d74ce748df3b95e756ec9e9da4e96679848f30001653d1ce06761c46220e704105f1850fb8eeab4ea61a26e4efe2786513e3c579ea1b754ad530b7036992486b866c6de31778bcb7504366f862ca8b5557a25b5ecc4b043a9432dcb048d49249e0020314c9f2f1edc7bf02a4a66089b01fa1eaa96dad2ea6e0d05ce700205c905c03a07d59f0579d2bb7569a85cfcd9d4c41b8466230c7c95b1c46cdf4465949f0ee07686b2daa098cb713af3ddeabcfd7e8d7d36e204548bbdac065a35226ef68d4c0205e2262c3737905ac6659dd5107524e07bbaf527a003249e0026a62633b05cc630bcc341738e41a093e9a0d4e405cad1f6a859df68f25d90aebb0346f8e4125421524065dcc406538c82430eb515482d9ced20f3c95fd8b1d3aa19769c45b519a10d04b81191800906fa10b965515ec5288beab152194e08e411d41ea08239041e411d28b044f106c41b820e608d415d27b29daf4d4156d2f085b91c063d24f7e7a0978f12f01fda4f355bb4ea4d8f7bf9bfbfbe8bcaeddb01a72f65e89b902e694fbb2723f372768e36ba9d71d2889444a2251146d4ff049bf36ff00c2d5bb331f587b85a3f23f54fb15c2057a15e49668eb056cd5252b453059d2b552059d2b52a40b34609c01c93feb0079d685642f577782c418e33ff88e848ff77d410083cdc7c3220f5eff00f91e46d5b54f659c9cf1fcadfc4f805ebfa2ba2498ab58401daa548e6e3987d407e6ead69cf3368074b5c95ecd2889445d6f467b2d334986e4ed8a331a33b63ab305059c805998c8f8f3c67030055f6c35a0e42012789dfe2bc5ed01f56ab9b7a8fc6f6b1b393584901a0d800c6ce96126eb6b15cc532096375642321810411ea181231524f88dfa2a4e6969820b5c2c5a41041dc41baaddd76f6ca0bbf9b2a978c1c34a08da0f42507fbc45f370467076071826135803198d4e9e1bc71f295d567463dccc566b88c4da441c4466013f35ced1a41d31613205035968dafa7685832195c823a104939523208e782382391c1aa8eccc652617a4d9e43198bb2e0c6820d8821a0411a1de34365087bffd7d9c7ef15aab0af5fec9585ee902f2cd65597bb2cbb8e4b15ce559394c49b4ec3184c8656f71b5d582d9120c4df58e195f485e77e5cfa75703cb1ccc618ec221ac6ba2ed759dd891883b1644715474469182a02cc78007249f2000c924f901c9aacbd093173000b926c0019924d800ba7f62f419349b6692e06d9a62091e6aab9eed1bcb76599980e9900f22aed266117ccfa0192f23d21b48ace01b7a748100e41ce746270e1668139c13915b4d6f588747b63712f27a2af9b37928f41c659b9daa09c1380777bb089f21bcaa9b3ece6b3b0b6dab9da31a3371f60352409198e457d7b2dfcef7339cbb9c9f4f40a0792aa80abd4e00c9279aa04cdce657b5a54c5368636cd6081399d493c49249e2547ad54a9444a225112889444a22ba7c9f6a70da2cf1dcca91ab14281982e5bc6242a188f211863f923ad59a2e89920651262f79fc3d1703a528979696b5cf203c3cb1a5d0d969602403a9791e2ba0839e455b5e6d449b4ab39a51712428d2a9043951b811ca9df8dc76900ae49c1e456a5a0de048d62fe6a66d673416873831c082c0e384836230cc5c58c0b8b15aeed7437373a7bdb5a44d2b3e33823c2148909dacc19d8ed0155158f24f1800e9501220099e568bf33e0acec2e6b6a073dc29864c4871c4e702d025a0b5a04c92e207ac729962785cc7229571d411823cf054e083f11547d0af64d707090439a722d2083c88b15e2b0b64a22fa493d688a4aea974b01b512bf7246dd8492b8cac9e1439553bd01ca80705973866076c472bc651a673973509a0d2ec785bd6038b18003c9c259770bb8617110644c1cc0222d6aa64a2251101c72288ae3a076f25b5c41a8e648fa09072ebe9bf3fcaafab13de72492fc0ab0cad16371bf51cf7fbf35c3daba303bb54a18eccd336a6edf87e03c3b99776e55f6cef60bd8c4d6ce2443e60fdb823aab0cf2ac032f4201ab60ce570bce3e9961c2e058e19875b848d08dc4483a12b3d65469444a228baa7e073fe69ff0085ab76663eb0f70b4a9dd3f54fb15c2457a15e49668eb056cd5252b453059d2b552052e284b2f78c42463ab37033d4a8c02cefc8f046aef839dbb72457ab55b4c4b8c6e19b8f21fa1c55dd9b657d73869b4bcea726346f738d9a39958a7d482031da6403c190f0e41e08400910a1e41dacd23024348158c63875f6b352c3b0cdc3377d63f80b735ee760e8765087be2b561713fc3a67e8b4f79c3e2765a0d56baa8af429444a225114b9f54ba9e216f24ac6250a0267c2028da9e0185c803ae324f2493cd6c5c4daf1bb4b656c940da0d692e0d687b89717c0c64bccbbb46f0774c0161651724715aa9d7ca225116482de5b971142acee7a2a8c9fb02e4f1e67cab204f13c168e78689710c68cdce21a0789b2ebdd9d59e3b08a1ba8cc5246a148241c81c2b031b3632a06e56dacad91820027a0c9813622da69ca7d5789da8b4bdc5845463dc5c080e6c171920e20d98330448220c898122d74ab3b362f6f0a46c7a95500fa91b80dd8fc5ce0790ac86819003966a37d673ece739e0641ce25a2381313c6254a775452ce40503249e000392493c0007249e00ad94404d85c9b002e4939000664ae71dbed462bd9e116f22c91aab7b2c1b0c48dd9da4f550983d0f38f3aa759d3110401a5effa85ea3a2e91607626b98f716f7c1692c00c44c6a5d3e13a2a95575da4a225112889444a225112889445d23b07ae0bab6f984a7efb08f0e7ab47e58cf9c44ecc0e02777efab945f223519711fdb2e50bcaf49ecd81dd60ee55ef464dabae5f18ed7176356dab0b8cb55ab6ba9a43a1bb422ddf812af8b0dcb6c9220a1806504a321909c3655719a8dcfc39e47517bee23db356e86cc6b038083559da349dd9c4c903131e4c1824070706c48826550fb55af58eb6165851d26438cb01864393e2da490c8c015078c3bf27caad478765208b731fdb4e657a3d87667d090e2d75378c50d26595040b48121cd9048d5adb2add42baa9444a225112889444a22511288a569fa9dce9b277b6ae51bcf1d0fb9d0e55c727da0719c8c1e6b60e232b7eb519150d5a2daa21e03869367378b5c20b4f237d642bd68df28104f88b505ee9febaf287d372f2f193d3e9ae792ca0e05a6d69cec778cbf31eabcf6d1d16e6de99eb1bf03a0541c8d9afff002bb400956e8668e741244c1d1b90ca720fbc30c83f654e0cf11c1715cd2d30416b8588702083c41b85eeb2b551754fc0e7fcd3ff000b56eccc7d61ee16953ba7ea9f62b848af42bc92cd1d60ad9aa75bdb49302c83c2bd58f0a33c0dd23908a58f0a0b02c78504f1503ea0609710d1c75e4333e015da341f58e1a6d75471d1a263893901c490167335bdb70bf7e71e7c88c7ae01db2cbcf42ddca820e565522b8f5b6f9b3047d3767e0dc87333c97b0d8bfe1ff9d5cf1ea699f47d4f70c9dd885d449ee64b86dd2b648e079003aed445011172490a8aaa33c0ae4b9c5c6492e275372bd7d2a2da430b00a6c1935a20733a93bc992752b156aa54a225112889444a2251128894456becaf68b4fd163db2a3b4b29f1b80308a0ed44009dce000647da3e9606e2a009e9bc377c9ccee1a0dfc4f3e0b8bb6ec952b9905a194c7eed84997b889738db0b493d86e23f364e1049378d23591ab07960422dd4ed576382e47b4522c6420facecac4f1b010d8b4d762cb2c813a9e5bbf50b815e875301c41aa462731b714c1c839f305c7e168222f88c89d956eaaaa47ca06b811069909f1361a5c1e8bd5226c7397389181c78427043d56acff009be27f01e39f96f5dfe8bd9a4f5aec9b2da53abb273c706896837ed17645aa835517a44a225112889444a225112889444a22cd6b752d9cab3c0c5245390475f423d082090ca410c09041048ac8317162168f60782d700e6bac41c8fe20837044106e082b6f276cf5677490cd8d9e4000adebdea0004991c60f03aa856e6a4eb4efcbc8f31afea1511d1f480230ce3d4925cddd81c6ede62e72322cbceb7da8b8d6e148ae1554a3ee1b3201e36e191d9f2c324abee1805942f2493ea1767163369f632b3b36c4da0496973b137090fc25c0ccc87343601812d8b900cd805a4a897412889444a225112889444a225112889444a2299a7eab77a6befb49190f981ca9fca46ca37b8952479115b071195bdbc464a0ab41b5443c07ee26ce1c9c21c3903075570d2fe5141c26a11e3f1e3fded131c8f79573cf44c702c36b6ff31f91fcfc170eb744eb4ccfd0a963c83c083c039a38bb7d89f59b3d4ece6f9a4aaec627f0f47f65b83136241eef0e0f966ad53782441198e0731a1bae26d1b3be98389ae6f64de25a6c727b65a7c0cef5c8d34b9546e9cac23a78ce1be3dc2879f1e8c63087a6eaec54db18ccce23f0b3b47d2c3c4ae06cdd155b68eeb0b5a7e7d4fddb237cbae7ec824e8b3836d6f9112995bc99fc2bef2b046c496cfb26499d08f6a1c9e39357a45ceb30060de7b4efc87915eb364ff86d8c8359c6a9ff00b74e594f917779dc630f0deb1cf752dc10646c81d07455f7471a80883dc8aa3dd5cc738b8c925c4ea4c95eae8d06521858d6d368d1a009e673278924ac55a2992889444a225112889444a225112889444a22df691daebad22dbe6b6ea872ccc4be4f50a00455640b82a58e4b6e27a0e4995b50b4408ce6f7fc9736bec0dacec6e2e1d90d01985b384924b890e26410065006674f30f6cb5688381313bce72c0360ff00e5ee0422fe201b0750a09248553bf3df7f2ddcb25b3ba3e918ecc6111d92e6e21f4e0cb8fd22711c8922169a699e77696562cec7249e49279249351933ccabcd68680000d6b44002c001900bc5616c9444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a22fbb4fa516257ca2ca5112889444a2251128898a225112889444a225112889444a22633444a225112889444a225112889444a225112888462889444a225112889444c62889444a2262889444a225112889444a22b2f63211baeae950492c10968c11bbc7ced214725bc3b4639c3301c9a9a96a7321b235bae4f48bacc649632ad40da841c3d811327202f266d201392cd376875f78d95e12148209ee48e30431ce38c0cf3e5592f77e7d9d146dd928020870904103ad06e0c81137be8b5fa7f67527b65bbbbb98eda390909b86e2db7c2e4282980a78ce49f500152da865a490d0729b9319ab5576b2d716318faee6005f84e16b3189689874922f90e13063cdf766de0301b6952e23b87d88ebc0df909b1c12db793d771e873b703382c88821c1c6011bf282b34b6c0ec589aea2fa0deb1ec7768e082e969004d865033104de25c9d98b281cc536a312c8a70cbb09c11c32e778c953c1e01c8e403c56c58066e008cec7350b76d7b84b68d4735d76bb10189a723184c48be67812142d37411791bdccd3a416e8fb37b73b9ba80880827c38639618078070c46ad64de4340b49d4f00a7adb56021a1aeab55edc781b030b7225ce3317b77799169fba9e80b696c2f2da74b9877ec62a082ad8dc01525b823cf39195e3073473204821c262da14a3b563760735d42a61c6d0e21c1cc98241005e78458de4429c3b1be216cd770ade3007b939cf2378532027c5b3c58119cf96570c76eab4918be1f589e5c157fda16c5d5d43b3831d7088201c38830816c56bbc71832147bdecc2c16f24f6f751ce60c77a8a082b93b32092dbb0d91c85c8048e783834e2e08761cc0d34f75253db4b9cd6b98fa22acf56f71043a062b8811237175c8d0c8d95ce996b79a758b5c5d25b11110032ee2dce491865c05e0739eb5b96820490db6baaaacace654aa1ac75797824b4e10d8160643a673d3250adf453a66a764c2459a19a456475e8c03287054e7054b2e46587239c8206a1b046a090411adeea77ed1d6d3aa21d4aa52639b518eb9697349690444830741965104e37d18ea3a85ebb4890c10cce5e47e8373baa285182ccc4600c81e59c950718649c8004c93c4985b0da3aaa74800eab52ad360653666ec34da5c49bc000e7079402479bcece471db497767751dcac58ef0282a5431daad82cc08ce73d38048ce08a165a410e8ce2c44ada9ed84b831ec7d03527ab2e21cd7168922c1b16e7722626568aa25d15b2d1f476d4da4264586289774923745078501460b3310703207079ce01ddad9e005c93a2abb46d1d5458d47d43858c6d8b88b9bde00046873ca2489973d9a885bc97165771dc772bb9d4295217a1604b3838f3071c79e700ec5969043a2e465655d9b61c41afa6fa3d61c2c7121e0bb300c06c4efbdf4892315968d63710acb35f470bb7542a495e4800b075072006e83ad60341d403b8e8b7a9b43da486d27d468c9ed7001d69903098836cf45e752d01ad7b96b7952e22b8256375e32c088ca1524e0ee6c03b8f20e718a3991110e0eb02378b42cd1dab1e20e6ba8be80c5518fbc3082e0e04013d913102c444cadd68da347a6ea4d6cd3a48e609449b41c4670a086c9cb60127a29e08c035235b06241384cc7cd54368da0d5a78835cc68ab4cd3c46f5449b88169223370be656a60d12c24670f7f1a046daa4a1f18c2b778be31819665c727284e706a30d1bc08cad9f1fc3c15d76d2f11149eec4dc4e01c060749180f64c9801d36b385979d47404b6b6f9edadc25cc2ac118a82a549e572a4b641e39c82091c119205902410e1306342b34b6a2e7607b1d41e5b8da1c43839a2c6e0360e7a116370601909d988228a37bdbc8ede49103842a58856e50b10cb8247518e0820138ace0de43491319d8e4a33b6b892194df59ac71617870682e6f780185d6dd798209025469bb373a5fa69d1b23b4a03238e14a105848782400a8c4801ba784b641382cbc58cdc1d233952b76c69a66a90e68a64b5ec3778782060190377082633bc415323eca5bdcb773697d14b39ce100237119665593730e809ced23033c0e46dd5cd8104eedf1c54076e734627d2a94e9da5e483843ac096e11a916917b66a1e97d9f179035ddcce96d0abf77971925f018a2a02bd14e7dacf07030091ab5922490d00c5f53b94d5b6ac0e0c6b5d5ea39bd6433b2053980e2e20e646e8caf2405f752ecfadadb7cf2d6e12e610db18a82a558f2b9525b208f3dc0f23008c9073204821c32316829476a2f7607b1d42a16e368710e0e68b182036fe0458dc1b2909d978638d1af6f2282491438420b10adca163b93048ea002010464e2b6eaf790d244c6762a33b6924e0a6facd638b0bc10d05cdb1810eb6ebcc1160b09ecbce3505d3fbc4c3277824fa3dde0b77b8ebd1586de9b87b5b4eeac75778b6533a4672b7f968eacd487765dd59a76c62ac818272f9c0cc4c7cd9ecafb75a1d84313c91dfc723282420420b11c840779c163c0383f0a168de0f08cf8233697b880693d80900bcb810d073711844819e6168cf4a897415afb49690dd6b0629e65b74ee90ef619190061700af2deb9f2a9de24dce1102e792e36c750b294b5a6b3bac70c0d384c1373243b2e5aa8c9d97b6b9263b2be8a69b04aa6d2bbb00b300db9f076827d93d39c0c918c00e4413bb298529db5ccbbe954a74e4073f10706e23009185b6920663849806bb50aea2d86a9a51d3c42e1bbc8e78c48ad8c75f6e3232de28f2bbb9fa438addcd88d438483ee3c156a15facc42303a8bcd3736716593a61b675e2da15b9d07499ecaead248e44135c46ee15d37044c128cc04919632af2b829b7041dc32a64634823297026089811adc67a2e7ed55db51b5010e34e83d8c2e63c30bdf8807004b1c0061b3a43a73106088679ecf16f3379ffa64f18c003dc38f415afcdfb5f82b1ff5f96cfcff00ea8dea66bba5c9a9eb46da22ab98d18b370aaaa8aceed804e00f77248190391b3db2e8cac33c800141b35614a8e232eedb9a1adbb9ce7547068131ff00a0601c947ff65e0991fe657b14f2a297eec295242f2db58b3027d3200248c903918eae7220902632b0527cb4b48eb29be8b1ce0cc648700e765221b6df7989807255ea8575158bb4200d3b4efcd37ef4a99f93791fc172f64fe256ff00c8df672aed42ba89444a22511288944569ec449203791db9c4ed0131fa9619dbb777872199783c7af00d4f4b58cf0db9ae3f4901fbb2efe136a8153380d744cc5e086bb2beebaf53cdda8eedbbdef766d3bb2ab8db83bb242e71b739c50e3e31af2d561add9a447578a461873e714f662f9cc283aaff003369ff0019ff00896b57775bf6bdd58a1fc6abff00e1fe47291be58f47b17b704cab72c500192581cc602f3b8960005c1cf4c567e688cf118e7a7aa8a01ad543acc341a1e498018443893a00d9be99adbd809b55ba116a1a62a2cbbbbc9bbb6420e19b7f78c32accc00cefce5b3926a41da3768139ba08f19e6a9558a2d9a75dce34e3052c6d7b48c4061c20c101a498c310320144d0e388e952a9b76bceeee72b1a9ebe10a8f26ccb18fae70ac09c654ae6b56646d8e1d90e562634536d2e3d6b7b6dd9b1d08754701605e4b9adc5003f289208d0830b5baddd6ad71105b981aded508c22c6638c1e8b92402cdcf1b988c925541273a3c9398c2d1a010d0ad6ccca4d32d70ad5dc0cbdcf152ab845f23616bc098cc980b6b3ff00ef6afe527ff696a43dff0011fca1536fff00487eabbffdc541b0fc1f56f80fe36ad07cefd6aacd5ef50e67ff00d61353d3ee6fb4eb07b589e50b1b2928a5b077746da0e0f07af5c1c51c090224da2d7d782c51aada752a87b9b4c97b48c6436461cc6289cc65bd4e789eddb4582505645662ca7861b9e22bb94f2b9c1c67d08ea0d6f11806441cb512e10ab87070da5c3b4c735a1ae176b8b69bc1839189196f0755ef4c48e593568e489a71df67ba43867c492b0036e1b01802fb7c5b41c649009bf3be75f2199871fd158ac481b390e6d23d540aafbb198a9304999130486cda626d256b756bbd5a4b76856d5ad2cc7251232ab8e306694a82d8c0c9f02b1c12bc0c68e2632c2ddc0103c4ebe9c95aa14e90707631b46d06c1ef7873a775360263581da20581ce7477ba7dc5832c7728519943007cd4e406e09f30410790460806a32233b6be0ba34eab6a025a43c34969226ce1122f1bc5c58cd895baecd4125c58ea1142a5dda38f0a39270cec400392700e00e4f41cd48c121da981ee550db1c1afa25c435a1ef971b012d6892741273361aac9a3e9f7363677ef7513c4ad01505d4ae49380abbc0c927c87a8f5acb5a4074c8ecc5ed7f15a6d155b51f4831cda84550e21843886817270cc0e6a65a4f7f65a55abe8f16e3277865658fbc6dc1b6a07386230b90323a0001c0ac890061bcce2204999b4e6a07b69d4aaf159d843300a4d73fab68696cb8b6e264c13075959754b9b99d34a6bf1b2e0cfe252369c078d558c7c6dca6c2781c9ce06715971270cd8ce596a22cb4a0c6b4d714ce2a429435c0e21269b8901dac3b1019e599586dbf9fef7f226fdc2b03bc793948ffe053fad4bdd60ecfbdcdb69525c6991efba338562177b08f6ee185c310a64f3c10727cc0230c90245dd306d2408ddcd6fb506baa86d538688a45ed05dd5b0d5c51736138788361be0c9d56f2fee34294ea8a524ef9426e5d848e1bd8c2e7186c1dbd01e4e2b67125bdab1911689f05150a6c6d76f5471b3ab717e177581a7bbdebc4cb6d399194a89da4d32eef64b79ad62796336d10dc8a58646e246501008c8e0f3cd6af69310091845c5fd94fb1d66d30f6bdcda6e15aa1c2f7069824418745ac7c96cd54c5af58a4836b0b55520f073b6652bcfd2cf18f5adfe70faa3d8aa84cd0aa45c1aee70232231d333ca2ea1cf3ea36a1a0d22c24b64e85fbb2f2b7a933152141f20bbb6f5471c635248b3416f1825c7c7f2f02a66b29be1d5aab2bbb314f1b59459b80a60892359c33939a6f3121b3b8bcd04c502349225e6e655196036152c5465bda600f19c9e7a1ad4025b6b90e9205ce4a77546b2bcb88631db3e16b9c435a4f5988004dbba0917f7086ca7b2d0275ba46899e74da1c6d27039215b071c1e718e0fa1a410d3369233b689d636a576e022a06d27622c21c049d4891a8f31bc2c5db1fc261ffe1a2fff00b562a663ea852747f75dff009aa7b85b0d4ee6eadb51b492ca3ef6416518d9b4b6e53de070553c58c1ea3a719e38addc48222e700b6722f392a9458d7d3a81e7ab67ca6a1c72185ae180b482eb4ceff0bdd7b7b617b67726ef4f5b4eea22e922a98cee1d23f105de18672b92300f192a4224196e181208045c697cd603fab7b30553b4758f14df4dce1506039bac4e18df63c6241a61e955d7795f668a393b483780ccb006453c82e1328369e188e580f22bb8608045a8ed6fecc81c40b2f36d711b3d8968354b5ee6d88a65fdab8c81b03be635859343d535d9ae826a1132dbed62c5a3d8170acca449b57e9855ea7209e3cc658e74dec2f32222dbd6bb4d1a2d6cd370755968686d4eb0ba5c0105b27e69272170391e7e7ad545e995ab45b3ff006834e5b1ce24b699587e665389b6fbd1b7498e9e155fa4313b46211ab4ff0095d9fadd71769a9f26a86a66daf4cb4ffe7a43f765dc1c30b7c5c742a4e9f7eb7dda5df1ff00268af1a7a6d44741b7f1598338fcae79ad81977012072008feea3ab4babd9e0f7dc5951fbcbea3dae33c40c2d3f556a07feeefff00cb1ffdb351fcdfb5fe9577ff00b8ff00f8ff00ff00a85614da7b4722b02435b6303a9ca212ab9fa4541c54bf3b9b7cec1730ff00f4e08b61ad32726c547438f004895aebab8d5238dadb4cb192d212304ac6c6461c83de4eca5b9cf978874ef08ad49390058391248e27f5cd596329921d56ab768a80c80e7b5b49a7e853062dc7b27e10aa555d76d6fb5e94bd869ea51976c4d82c301b94398c8249038ce429c1538208352bf26e6201cf5cb25cdd94454ab76ba5ed90d2496f7ace9020f22722330b43512e925112889444a225117b8a57858491b1561d0a9c11ef0ca4107de0d67d16ae687082039a736b80734f30641f152db5bd41c156b998823041918820f04105b0411c107ad6d88ef3e6542366a632653045c10c60208b820c588d0a8af3c8e8b1333144ced524955cf2fb149dabb88cb6d0327935acf90c86827385306804900073a3138001cec366e222ee816124c0c97a5bb9d15516470b1b6e4018e15baf79180708f9e77280d9e7349e76b8e0778dc56a69b4c921a4bc61792012f6e585c625cd8b41911a2cefad6a0ea55ee662a4608323104742082d8208e083c11c1adb11de7cca8c6cd4c5c329822e0863010464418b11a2c16d793da12d6f23c64f04a315247a1284647b8f19ad418ca472b7b291f4dafef06bc0b80f68780778c40c1e4b2dc6a979729ddcf3cb227d567661c720ed662320f20e38ac971399247124fbad5b418d32d6b18ef89ac6b5d06c6ed00df558cdedc19be72647efbebee3bf81b07df33bf85f08e785e3a56275bcef9bf9e6b6eadb1861bd5ffdbc2305ce2ee461ef5f2cef9af2b7332875576024f6c027c7f4bef801c3f249f1679e693eb9f1e7bd64b018b34967709009669d8b766d6b45acb25b6a57768bb2de692352738472a33ea550819f7e33590e2322472247b2d5f45afbb9ac791697b5af31ba5c098e0bcbdedc49289de47694630e589618f6489092c36f960f1e558939de77cdfcd64536818435a1866581a030ce72d03099d645f55f23bc9e294cf1c8eb2924970c43127972cea4312c796c9f11eb9a4eb7077ebe6869b48c2435cc10030b41600db3406905a308b0816192cd2eb17d3218e5b895d1b82acec411e8cacc411ee22b25c4ea4f32485a3767634c86536b85c39ac6b5c0ef040041e4be6a3a83dfc8aefc04458d4649c2a0da32cdc92cdb9c93f498d1c67c000390fd4a51a4298205f139d51c60365cf3390b00043470014cd1b571a75b5d206749665411b2704152c589705593c2d804649e456cd74039826208d20efd141b450eb5d4cc35d4e9b9c6a35f704380006120875c490542b8d4eeee9765c4d248bd70cecc33e476b31191e471915a971399279927dd58651630cb5ac63b29635ad31ba5a018e0bcdb6a175680adbcd2460f24239504f4c90840271c64f38a0246448e448f6597d26beee6b1e45817b5af206e05c090382f92dedc4d209a591de45c61d989618e54abb12cbb4f2b8230791835899de4ef264f9acb69b5a3080d6b0ccb1ad0d61c5632d000322c645c58af82f2e16469848e247c866dc77303ed067cee60df481273e749e72733a99de53ab6c06c370360b5b8461691916b62011a40b6896d793da12d6f23c64f04a3152475c12841233ce0f19e680c652395bd91f4dafb3835e05c07b43c03bc070307885f6e6fae6ef1f3895e5c74dec5b1ebb77938cf9e319a124e7279927dd1949aceeb5b4e73c0d6b26329c204c692ac369a86976f12c6977a84781caa300a0f56d8a00006ece38c9f3c9a98103578e00803c172df4aab89269ec8f93673dae73c8196224e711f82d6f687585d4aed67837aac68a8a58f8cedcb778ec09c3966272189e0312092068f7499122000273b6a78f8ab7b26ce6930b5d85c5ee73dc1a3f7631c0c0d040ec8032803488583eeeea5fd2a7fed1bfcd58c6779f33f9a93e4d4fe0a5fe1b3fdaa3c17d736cc5e095e366ea558a93e7e228416e493ce79e7ad6a091948e4614aea4d740735af0dee87b5ae0dd3b21c0c5ad6d12e6fee6ef1f3995e5c74dec5b1ebb77938cf9e319a124e727999f7586526b3bad6d39cf035ac9e784095e26b896e08699d9c801416249007b2a0b1242af928e0790a133c79ee5bb581bdd01a09c44340682e39b881124ea732b2aea374aeb2acd2091576ab6e3b828ce2357cee54193840428c9e39acc9de645a64c81ba772d3a96c118585ae38dcdc2dc2e79cde5b105c6076889b66bedc6a979729ddcf3cb227d567661ee255988247971c7950b89cc923892561b418d32d6b18e1f39ac6b5c273b800dd45ad54cb349773cb2099e47690630e589618f670e4ee1b7e8e0f1e58accf39dfadb8a8c5368184068619960680c339cb40833adafaacd2eaf7d3a18e5b895d0f5567620fb99598823dc41ac9713a9238930b46ecec6990ca6d70c9cd634381de08120f10a1d6aa759ed6f67b362f6f2346cc0a92a70483d4123e008f3040230466b20c656d2d651be9b5f6700f00e201c240235bfe88b1b2f105c4b6ee2485d91c7465254f3c1c3290464120f3c8383406381e16597303843807b4e6d700e698b8906458dc6e29dfc9ddf71b9bbaceed993b777b3bf6676efdbe1dd8ce38ce293e59c693be167009c5031c61c7031e199c38b3c3378989be6bd3ddcef289da47328c61cb1dc31c2e242770da000b83c018149d6f3bf5f35a8a6d03080d0c39b0340619b996c61b9b9b5f5527eeeea27fe2a7fed1bfcd5b633bcf99517c9a9fc14bfc367e4a0568acac924f248aa8eecca830a0924283c908a490809e4850013cd667d32e1c96a1a0490002ebb88001711917117711a132b1d616c9444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a225112889444a22511288bfffd9504b0304140008080800d57c6e440000000000000000000000000a0000007374796c65732e786d6ced1cdb92dbb6f5bd5fc151269db6534a24a5dd95d4ec66d69eb8c98c37717de9ab074b4212638a60417025f9073afd94f4a90ffd82fac77a7023c1ab2869eddd6e93641c0be7003838f7830bbff976bb8eac3b4cd390c4970377e80c2c1cfb2408e3e5e5e0dddb17f674f0edd56fbe218b45e8e37940fc6c8d6366a76c17e1d482ce713a97c0cb4146e339416998ce63b4c6e99cf97392e058779a9bd87331956c1183f5ed2e90cdde0c6f59dfce1cb7d417ddf69f59209bbd038a367d3b735ce0a9d97d41fa76dea691bd20b64fd6096261858a6d14c61f2e072bc692f968b4d96c869bf190d0e5c89dcd662301cd09f673bc24a391c00afc118e309f2c1db94377a471d798a1bef4715c93a4385bdf62da9b3588a19a54d3bb656f8db85bb6b0c65f21da5b37047259bce3a0bf78c781d9778dd8aa4526d3d10d00c51f372f0b5da0ebbe7371dc12ab7c1a26bd9729b1cdfe84909c54de411aa820d7739cc948fe36b0379de81b1a324c0d74bf13dd47919f739cac9b980678ee08306c7cc7d5546353bee8d691cf46142784b29c90457f0705dcf172f35ab175d46e5e1caa519734081a51819cf1084c0d14ddbe0bf1e6ab41c973760b605611807043fbba0824d34f7576709d11c7c9cd0644523855baccddfe8264312c0242856220de2698861c8422d16d5e1ac1d4b2881c31a48a15c60825f34ed3316be2f7dbd7230eb3b9bb0787a64631a29c37b8d2216d41209c2d908fed00fb517af58d744579b3257f73e22e073f01e7deecd6b704640e6e4763adc36857028eba87798b56648ddc863124646f7fe04f6afd8837d66bc08e1bc6f92d4a48faa70a9e6c1c58a5a139bebdc431301d0c95aaf10a8c24643eb8b23b4443a1537b48bb06b426e6e8f6f6a9d34d98a6274fddc4d4fadc2820341631ef72f023b8d32f429af52e0e21b3c2d6cd9b568955117b882cdda50caf4fa1eee68d7513c6fe8ab49295637c117a9411b45ac7e9b38fda8c5fb5cbf4565319e005ca2295f4ea9115494b8a9255e80f34aefa6d2714bc2c6521d81f5f46ca28f980214d8908e4455f8d27e7676832b0b8d79d2fc228ca2117de6ce12f06d682cc3730944d12267c564c6cfe5b754957a0c11b1ba84d31b3b797036738f6d78dc05d05c820cbb12129c4769a201f52527b4568f891707fcb51bd6927f21d5f945f478528da77d41a6ac3988ac911ac6313b2952d93fc058a52437b124491e07789db02c4f16d9431c2e700950a034c242a8a9255ae42828c5b8a1124d020a2d0671ac2b3184edb1aecf07210519bdd96942a8c03cc53075e0c998bd1446a1a211083de9024e55ad74e768ecee9aead264b31b021e65215932b656134c325a25aa203e89200a7e14700bb5ec2445b84e2658696d0b4a0a2c18758cc2828cc8bd73983308364cefe80692c16d738a50d29158a1bbd5c81c827d78892043583a242c33e6eb71aa4e8d19098c40d73f3cc3fc2db46e720e6cc111a67cda10df3e630317321c0923fe8e32472890f3a551744b0da252b1c8b7cc88e501000ef0535c20144e13acc57d053c3932cf6592607e40e04d225583ac872bf0968d5b583101c41cc2781cce9cc2decb36c2409f0b330ce5f35f9d16ab2a968583bacaaf651bc46616cf3aa58aba057434ab274554139c14c64d16278ce089b1a24f75f6e09e556c1550e0206e84f849294ebf3a913db946c2a93434bc53e3f609cd88c2c315bf10d0e6e7ffb263627946afd06ac29403418b4ba092dbe08a590a17253d24d6b947235e6b6a68c64af4f59230a066f4778c105349ccc66dc820b000d972b8038e55630fc5adb2d618c97e8ced09583682d8b3989198aaa5190f7958e2a0a97a0f83f67290b173bdd51fdb4b9fc80dd1b106dad672862ac224576e35cb74b40d547f381f3466d416909753925c3e3e81a423b9af61242fc55f5ba9712a2eadc5cb0f142b5f6a8d4f7180586536fd52868c015e2abda18731e99086fa1e1bde7bcbf25c1ae49337bea9f54a8e1c41bb72995e77a7d954ad8a1c80c639119a2688376e9a182ee23e7d30539299c752dd098a5576d4e85736809a6fbb595421a5e10dc16dc1a29ef0a538a901e289ad61cb595da1ca39dde22304e0e319a26c5865c2789d0ce507dcb049f625847dbcc89f6d29b1f2f21cf3b66a11d0e22e243f634caaadab87d54abf7e29e235149dfa3206135141de8fd94c84ef17ee20c4b86375179a66588c8e775f473da3d624b2a5dc4362829a0780ffd5eb9af111215b4a1f3d142ee97091b34e47045456f2df901128aed3dea4828c6ebd4917b92e71735a6b7dc0f73937fcef5549cc934ba4f8e665571be8ce97d1eb63eaa0cd13928d869915552c526899d904db66ac6414ed3ac197c18859f27ee2f19eed9336eb02c8c6e491494e42201dad1b58173e312080765f2c5721fa3add4b636ab0d9a44928672f37a3674a7c6b611db253897eb686f6f773a1c9f7bd5eea26a35d86a50d3b9cfda4f0ad77c47da48354f10034f81cabe407012f4979b897b3fa9e0f85e13c11784b027a883b3f1493a389d7d591d545e586a49a3b75618967b8ca82a4ebe57d9af37b848c68440227cc777e1dc8ab84b25ce9ec4d33dfb7ab0dfe596f24ed1a5bf3fae648c2dbd4f70d74a485eb790bc071692778290261dd5c161b2ab6e29c8c5b4d70c87c9b679f46a35f059a43fee96fef881a53fbe2fe91f27e6d3c4789a905e45c8c72be8520f69bdb820f26fbecb9c420a1fd93e4a5275c8248fec1d670aff96cee53228fba83c1696c61240a4c6410bce260cf86d41b155dd8ca1a62a8eccfaafbe946dc8eb492dd55a8e6955d04adc1ab54f8564d2f4fe6cf19e858ce2e669345609a597400c8d7874b5989bb06e0d484914dea7023c7099f22c8b22ccf6ea9444eb54a8ded2362fdf55255eba9857eea819d08252de6bef1a27e754f7484d68bdd9fa67797da8c6a91ed78c449a8cf80901b5657a6a8698bbe57cab0fd1e0efbbd2819abc18609e4aeb2b3936a4c0202792d4009447955a102b6efdc89ee5e2dd8056bad7182456a363589d4f3f49c8a08228829d429765432541850ac2e6f7a711330e4e458526bb16fce414b58ddd7356ef41661d3fc8ac930799f5ec41663d7f90592f1e64d6e983cc3a7b90595de7bea71dd5dd98a2a5d8156acfd65aaafe224f7307e668c6cc6aa7a4ea03c54f33156acbd8f8fa138a17e136df0ab34c589a2d04cc9a3771ccedc9b216aa0f5893f704d7347e826b9a3cc1359d3dc1359d3fc1355d3cc1354d9fe09a664f704d3ca3f85f59940136139598309c42511b2fc26546c5b55f2b07d86a37714108e3bf9b09d32ce08f49ee5094617e82231b75c7d42ece5ac43d72b34fac131e9b8fa75f0ef29ab13f85380eda080c9b091ce5728e714141d334ad8756f2098eb8f568de976de28e1aa4e002bfaeab6061ec53f1429b071de3f59218ad78b4c4b78f60ccd0b735c0a8a84b59a638a44759ed4462cf056cd9ac36abf889985893bc96ad0eebe5c1647ae0b504206578dd480cdfeccad6718526d958274db56b0acf87cef4a2603bc5915dc638bb187b933f1c41ecb37b27d61b8e9d4917ad53cf758f21f5ba7638a8c8c25154a5149a2a1be0090ae4a720f4c9b3bc7e5f7b12d38f9ada2148af1bff27aad6f85e54ab2e10cf9d4ece0e97c8f87148e4e6d5d187c6981e747f073caf1eb0e3f2ce01841f75905abf97f459ee20f5bc6e39ad9e531a67678db07c53797ac89daf9b57471d3b1eceabd21313a7f379c9e1aa71cc5390c72687c997d7d9de76f79818f5b6ee1bf71cd1aa777df955279e6b1981b4faecef506fb3a0358af4594cbbe0f2539cae57e132ac64b1cd5694644b8828b7c8ffb0a4fc8318e5831903c9482179bac8e1090eec5c1fd2fa43d1f251ce8292b57dfc798ee82e53d31a42f308eb9052428d97877e14f235619ffd0e0cf98f56e98fdfab37e21068c358be3975be566dfc621b4529339a280e8c5f4b8a716cfcbe95997c0e46eb35e2359931624428788efccb20cae10860b8e6efc808d72bb66be8261fbda6fac25b8b4a89c76811da918c95152b599b57ff72a45ab45fe222e1183a8eabdca800acd4d1af371b5eb496176a0aa85e980dc2e21f56517516a1c0cf90d55efdb943773c6dbe690824541e30489fef0ec7d5775dcaefbb436f3a6d7b325c79572f6b417b8db6f9d2b8af2fbe16a11052ac6fe829d63843c73526d10f96ed5b0c8c10f8e2d1a2e336e0a085f01f0d28e6f1bd79388a02ee50654166da02cf11b5ac8a4b66c53511fe8fa127cd52d78b5d894cabfc9454b52dc4f5cc6a080696e74c1b9e3709a93b30d7ee94966ad660077acb4b4bed67c5fbe962314de42a3afb2d819f4a6bfadde1b93b397a05faa16127f9d53850a6b52ea11ef6ec3d667b9eb5dbf3458b41bbcdef84b941cf7e35e82f6ad0de70567db5fd194cba6a13f76cd3cec5f4f3da74c525ed37e951ebce9902a8d7f6ba556534c513fc920ba8de7d37d5c108faba4ff532e79b2c6422db47946256ec8a48eeaacdc8a4be9dcc2b790565e856ef5a0253e2065cc8afaf5e7ffac7ab77cf5efef09777df592f5e5ffff8e9efd73fbcf94eedfff27e7a2f38a9684259076022b13d23fe545b35d5ed45d95aab6938acd45d6dad74e00fafc586ec415d9e55bb50b2a98c81a3a87352b7f87a1cdf175617abf87752f8b55cd913149e4136cb5351a1815da2f2f26de516f0d5f79ffecd706405d8fa6b1801634796b8bfca1b2264bdfef44b92dd46e1df320e79f6cabaf0e0ffe7b38ba963dd401448e1d75be83eb79c09877963ebdce3ff8dac1768ab9aa796e75aced43a9f14823e816471b45061ea7db2b96bf6f1e0ea9ad24fff649f7eb1e2fffccb92a859cadd5188a3c05e62a636d8450f88a59892f7018e42a89f44e01c5cc946c58b72dfab3e1c02225e7167a0508acf4f28641c817a087f7139f033cacbc5c195ab472ed0af46c608e2832a652cd96418679deba39aba9b2ded8b502f7eaace33ff69f8bcbd6eb0e2c71aefb00824ab8cd1e82fbd03fda0fca21a8516fd0933034314f3a2593688122fd54730fbee4e42aa34addd9f84bfab0c613c74547432a21e046ecfcdbf7cf651ec526de503a6a2c8b4c4477ae72b8a17978357a1cf328ad391eb18ffb8e7dfc9ff9fcd5e4c2e5ccf797e3dfc39011724bb2a7b09f99e8b6e4b57fc43ad18142bd04d487c0a0a3049fc922059b3160c7b944164dc1144c60706117112a0e690fb27b2476a539c60243cf8f8c49831fe4c31e3240ffbff42d4645f3408e324abc683dc4f89e600cb0f248b8a6a30eae5ff27c7f87fe751f9ff37f56dacae08306ace8f47cd9f89bffa2f504b07089957fedaea0e0000665e0000504b0304140008080800d57c6e440000000000000000000000000c0000006d616e69666573742e726466cd93cd6e83301084ef3c8565ced8402f05057228cab96a9fc0358658052ff29a12debe8e935651a4aaea9fd4e3ae4633df8eb49bed611cc88bb2a8c15434632925ca4868b5e92b3abb2eb9a5db3adad8b62b1f9a1df16a83a59f2aba776e2a395f96852d370c6ccfb3a228789af33c4fbc22c1d53871480cc6b48e08091e8d4269f5e47c1a39cee20966575174eba09079f7203d8bdd3aa9a0b20a61b652bd87b6209181408d094cca8474831cba4e4bc53396f35139c1a1ede2c760bdd383a23c60f02b8ecfd8de880ca6e55ee0bdb0ee5c83df7c95687aee637a75d3c5f1df2394609c32ee4feabb3b79ffe7fe2ecfff19e2afb476446c40cea367fa90e7b4f21f5547af504b0708b4f768d20501000083030000504b0304140000080000d57c6e440000000000000000000000001a000000436f6e66696775726174696f6e73322f7374617475736261722f504b0304140008080800d57c6e4400000000000000000000000027000000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c0300504b0708000000000200000000000000504b0304140000080000d57c6e4400000000000000000000000018000000436f6e66696775726174696f6e73322f666c6f617465722f504b0304140000080000d57c6e440000000000000000000000001f000000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b0304140000080000d57c6e4400000000000000000000000018000000436f6e66696775726174696f6e73322f6d656e756261722f504b0304140000080000d57c6e4400000000000000000000000018000000436f6e66696775726174696f6e73322f746f6f6c6261722f504b0304140000080000d57c6e440000000000000000000000001a000000436f6e66696775726174696f6e73322f706f7075706d656e752f504b0304140000080000d57c6e440000000000000000000000001a000000436f6e66696775726174696f6e73322f746f6f6c70616e656c2f504b0304140000080000d57c6e440000000000000000000000001c000000436f6e66696775726174696f6e73322f70726f67726573736261722f504b0304140008080800d57c6e44000000000000000000000000150000004d4554412d494e462f6d616e69666573742e786d6cb554cb6ec23010bcf31591af556c82fa8c08a842e5dc03fd00d7d90423bf64af11fc7d135a2055052282fae25d7b3c33f2ae3d9e6eb44ad6e083b4a620191d92048cb0a53475413e16f3f4994c2783b1e646561030df074973ce84435a90e84d6e799021375c43c851e4d68129ad881a0ce6bff1f94ee990750c8cc864901cf52aa9206dcefbed115d45a552c7715910768ae4b8aca1943cc5ad838270e794141c1b185b9b92ee0cd3ae4f8ab041c2fa78582ca3fe345caac0701f5267ea131ea4e635b076bf978ab0065b7fcd3d9e206e9db376bb176f00c4a6d8e1e6c41a90df9cf45d0a8c1e02cb869d913dbe7dcf0f2ff3fba76c349cbdd2953b5f8095839e1508b855f00ff7f4b3467d595dd0b60deaaeb7c6cc9a4ad6d1ef28c28871214041935acf44f4fe7c635da775e1530cd1b41668945474195af131fbf3ff4cbe00504b07081a2a3b0741010000ba040000504b01021400140000080000d57c6e445ec6320c27000000270000000800000000000000000000000000000000006d696d6574797065504b01021400140000080000d57c6e448c34972fc6130000c613000018000000000000000000000000004d0000005468756d626e61696c732f7468756d626e61696c2e706e67504b01021400140008080800d57c6e4460f7ef35f7070000152a00000b0000000000000000000000000049140000636f6e74656e742e786d6c504b01021400140008080800d57c6e44a8dba4ded7050000f92600000c00000000000000000000000000791c000073657474696e67732e786d6c504b01021400140008080800d57c6e44642c5ba30c020000a904000008000000000000000000000000008a2200006d6574612e786d6c504b01021400140000080000d57c6e4427e348f227c6000027c600002d00000000000000000000000000cc24000050696374757265732f31303030303030303030303030313645303030303031353946343731323043412e6a7067504b01021400140008080800d57c6e449957fedaea0e0000665e00000a000000000000000000000000003eeb00007374796c65732e786d6c504b01021400140008080800d57c6e44b4f768d205010000830300000c0000000000000000000000000060fa00006d616e69666573742e726466504b01021400140000080000d57c6e440000000000000000000000001a000000000000000000000000009ffb0000436f6e66696775726174696f6e73322f7374617475736261722f504b01021400140008080800d57c6e440000000002000000000000002700000000000000000000000000d7fb0000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c504b01021400140000080000d57c6e4400000000000000000000000018000000000000000000000000002efc0000436f6e66696775726174696f6e73322f666c6f617465722f504b01021400140000080000d57c6e440000000000000000000000001f0000000000000000000000000064fc0000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b01021400140000080000d57c6e440000000000000000000000001800000000000000000000000000a1fc0000436f6e66696775726174696f6e73322f6d656e756261722f504b01021400140000080000d57c6e440000000000000000000000001800000000000000000000000000d7fc0000436f6e66696775726174696f6e73322f746f6f6c6261722f504b01021400140000080000d57c6e440000000000000000000000001a000000000000000000000000000dfd0000436f6e66696775726174696f6e73322f706f7075706d656e752f504b01021400140000080000d57c6e440000000000000000000000001a0000000000000000000000000045fd0000436f6e66696775726174696f6e73322f746f6f6c70616e656c2f504b01021400140000080000d57c6e440000000000000000000000001c000000000000000000000000007dfd0000436f6e66696775726174696f6e73322f70726f67726573736261722f504b01021400140008080800d57c6e441a2a3b0741010000ba0400001500000000000000000000000000b7fd00004d4554412d494e462f6d616e69666573742e786d6c504b05060000000012001200cb0400003bff00000000', '2014-03-14 16:39:28', '2014-03-14 16:39:28', 1);


--
-- Name: modeltemplates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modeltemplates_id_seq', 2, true);


--
-- Data for Name: modeltypes; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modeltypes (id, name, description, created, modified) VALUES (1, 'Toutes Editions', 'Toutes Editions', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (2, 'Projet', 'Projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (3, 'Délibération', 'Délibération', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (4, 'Convocation', 'Convocation', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (5, 'Ordre du jour', 'Ordre du jour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (6, 'PV sommaire', 'PV sommaire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (7, 'PV détaillé', 'PV détaillé', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (8, 'Recherche', 'Recherche', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modeltypes (id, name, description, created, modified) VALUES (9, 'Multi-séance', 'Multi-séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');


--
-- Name: modeltypes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modeltypes_id_seq', 10, false);


--
-- Data for Name: modelvalidations; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1, NULL, 2, 1, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (2, NULL, 3, 1, 1, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (3, NULL, 4, 1, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (4, NULL, 5, 1, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (5, NULL, 3, 2, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (6, NULL, 4, 2, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (7, NULL, 5, 2, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (8, NULL, 4, 3, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (9, NULL, 5, 3, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (10, NULL, 2, 4, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (11, NULL, 5, 4, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (12, NULL, 2, 5, 1, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (13, NULL, 4, 5, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (14, NULL, 5, 5, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (15, NULL, 2, 6, 1, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (16, NULL, 4, 6, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (17, NULL, 5, 6, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (18, NULL, 2, 7, 1, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (19, NULL, 4, 7, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (20, NULL, 5, 7, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (21, NULL, 2, 8, 1, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (22, NULL, 4, 8, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (23, NULL, 5, 8, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (24, NULL, 2, 9, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (25, NULL, 3, 9, 1, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (26, NULL, 4, 9, 0, 1, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (27, NULL, 5, 9, 0, 2, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (28, 1, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (29, 1, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (30, 1, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (31, 1, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (32, 1, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (33, 1, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (34, 1, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (35, 1, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (36, 2, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (37, 2, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (38, 2, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (39, 2, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (40, 2, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (41, 2, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (42, 2, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (43, 2, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (44, 3, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (45, 3, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (46, 3, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (47, 3, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (48, 3, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (49, 3, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (50, 3, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (51, 3, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (52, 4, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (53, 4, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (54, 4, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (55, 4, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (56, 4, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (57, 4, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (58, 4, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (59, 4, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (60, 5, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (61, 5, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (62, 5, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (63, 5, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (64, 5, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (65, 5, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (66, 5, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (67, 5, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (68, 6, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (69, 6, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (70, 6, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (71, 6, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (72, 6, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (73, 6, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (74, 6, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (75, 6, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (76, 7, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (77, 7, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (78, 7, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (79, 7, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (80, 7, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (81, 7, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (82, 7, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (83, 7, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (84, 8, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (85, 8, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (86, 8, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (87, 8, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (88, 8, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (89, 8, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (90, 8, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (91, 8, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (92, 9, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (93, 9, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (94, 9, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (95, 9, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (96, 9, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (97, 9, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (98, 9, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (99, 9, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (100, 10, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (101, 10, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (102, 10, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (103, 10, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (104, 10, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (105, 10, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (106, 10, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (107, 10, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (108, 11, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (109, 11, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (110, 11, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (111, 11, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (112, 11, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (113, 11, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (114, 11, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (115, 11, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (116, 12, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (117, 12, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (118, 12, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (119, 12, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (120, 12, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (121, 12, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (122, 12, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (123, 12, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (124, 13, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (125, 13, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (126, 13, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (127, 13, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (128, 13, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (129, 13, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (130, 13, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (131, 13, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (132, 14, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (133, 14, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (134, 14, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (135, 14, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (136, 14, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (137, 14, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (138, 14, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (139, 14, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (140, 15, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (141, 15, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (142, 15, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (143, 15, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (144, 15, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (145, 15, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (146, 15, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (147, 15, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (148, 16, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (149, 16, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (150, 16, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (151, 16, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (152, 16, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (153, 16, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (154, 16, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (155, 16, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (156, 17, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (157, 17, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (158, 17, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (159, 17, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (160, 17, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (161, 17, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (162, 17, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (163, 17, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (164, 18, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (165, 18, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (166, 18, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (167, 18, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (168, 18, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (169, 18, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (170, 18, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (171, 19, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (172, 19, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (173, 19, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (174, 19, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (175, 19, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (176, 19, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (177, 19, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (178, 20, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (179, 20, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (180, 20, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (181, 20, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (182, 20, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (183, 20, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (184, 20, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (185, 21, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (186, 21, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (187, 21, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (188, 21, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (189, 21, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (190, 21, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (191, 21, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (192, 22, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (193, 22, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (194, 22, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (195, 22, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (196, 22, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (197, 22, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (198, 22, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (199, 23, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (200, 23, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (201, 23, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (202, 23, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (203, 23, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (204, 23, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (205, 23, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (206, 24, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (207, 24, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (208, 24, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (209, 24, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (210, 24, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (211, 24, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (212, 24, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (213, 25, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (214, 25, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (215, 25, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (216, 25, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (217, 25, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (218, 25, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (219, 25, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (220, 26, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (221, 26, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (222, 26, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (223, 26, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (224, 26, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (225, 26, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (226, 26, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (227, 27, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (228, 27, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (229, 27, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (230, 27, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (231, 27, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (232, 27, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (233, 27, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (234, 28, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (235, 28, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (236, 28, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (237, 28, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (238, 28, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (239, 28, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (240, 28, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (241, 29, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (242, 29, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (243, 29, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (244, 29, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (245, 29, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (246, 29, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (247, 29, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (248, 30, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (249, 30, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (250, 30, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (251, 30, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (252, 30, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (253, 30, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (254, 30, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (255, 31, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (256, 31, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (257, 31, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (258, 31, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (259, 31, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (260, 31, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (261, 31, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (262, 32, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (263, 32, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (264, 32, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (265, 32, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (266, 32, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (267, 32, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (268, 32, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (269, 33, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (270, 33, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (271, 33, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (272, 33, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (273, 33, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (274, 33, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (275, 33, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (276, 34, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (277, 34, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (278, 34, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (279, 34, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (280, 34, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (281, 34, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (282, 34, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (283, 35, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (284, 35, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (285, 35, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (286, 35, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (287, 35, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (288, 35, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (289, 35, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (290, 36, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (291, 36, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (292, 36, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (293, 36, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (294, 36, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (295, 36, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (296, 36, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (297, 37, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (298, 37, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (299, 37, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (300, 37, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (301, 37, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (302, 37, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (303, 37, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (304, 38, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (305, 38, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (306, 38, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (307, 38, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (308, 38, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (309, 38, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (310, 38, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (311, 38, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (312, 39, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (313, 39, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (314, 39, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (315, 39, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (316, 39, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (317, 39, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (318, 39, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (319, 39, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (320, 40, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (321, 40, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (322, 40, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (323, 40, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (324, 40, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (325, 40, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (326, 40, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (327, 40, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (328, 41, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (329, 41, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (330, 41, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (331, 41, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (332, 41, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (333, 41, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (334, 41, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (335, 41, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (336, 42, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (337, 42, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (338, 42, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (339, 42, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (340, 42, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (341, 42, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (342, 42, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (343, 42, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (344, 43, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (345, 43, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (346, 43, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (347, 43, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (348, 43, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (349, 43, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (350, 43, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (351, 43, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (352, 44, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (353, 44, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (354, 44, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (355, 44, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (356, 44, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (357, 44, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (358, 44, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (359, 44, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (360, 45, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (361, 45, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (362, 45, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (363, 45, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (364, 45, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (365, 45, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (366, 45, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (367, 45, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (368, 46, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (369, 46, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (370, 46, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (371, 46, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (372, 46, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (373, 46, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (374, 46, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (375, 47, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (376, 47, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (377, 47, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (378, 47, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (379, 47, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (380, 47, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (381, 47, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (382, 48, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (383, 48, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (384, 48, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (385, 48, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (386, 48, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (387, 48, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (388, 48, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (389, 49, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (390, 49, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (391, 49, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (392, 49, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (393, 49, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (394, 49, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (395, 49, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (396, 50, 4, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (397, 50, 4, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (398, 50, 4, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (399, 50, 4, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (400, 50, 4, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (401, 50, 4, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (402, 48, 4, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (403, 48, 4, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (404, 48, 4, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (405, 48, 4, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (406, 48, 4, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (407, 48, 4, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (408, 51, 5, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (409, 51, 5, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (410, 51, 5, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (411, 51, 5, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (412, 51, 5, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (413, 51, 5, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (414, 52, 5, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (415, 52, 5, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (416, 52, 5, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (417, 52, 5, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (418, 52, 5, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (419, 52, 5, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (420, 53, 5, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (421, 53, 5, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (422, 53, 5, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (423, 53, 5, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (424, 53, 5, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (425, 53, 5, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (426, 54, 5, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (427, 54, 5, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (428, 54, 5, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (429, 54, 5, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (430, 54, 5, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (431, 54, 5, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (432, 55, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (433, 55, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (434, 55, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (435, 55, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (436, 56, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (437, 56, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (438, 56, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (439, 56, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (440, 57, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (441, 57, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (442, 57, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (443, 57, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (444, 58, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (445, 58, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (446, 58, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (447, 58, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (448, 59, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (449, 59, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (450, 59, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (451, 59, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (452, 60, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (453, 60, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (454, 60, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (455, 60, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (456, 61, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (457, 61, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (458, 61, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (459, 61, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (460, 62, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (461, 62, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (462, 62, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (463, 62, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (464, 63, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (465, 63, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (466, 63, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (467, 63, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (468, 64, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (469, 64, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (470, 64, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (471, 64, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (472, 65, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (473, 65, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (474, 65, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (475, 65, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (476, 66, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (477, 66, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (478, 66, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (479, 66, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (480, 67, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (481, 67, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (482, 67, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (483, 67, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (484, 67, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (485, 68, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (486, 68, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (487, 68, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (488, 68, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (489, 68, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (490, 69, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (491, 69, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (492, 69, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (493, 69, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (494, 69, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (495, 70, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (496, 70, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (497, 70, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (498, 70, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (499, 70, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (500, 71, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (501, 71, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (502, 71, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (503, 71, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (504, 71, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (505, 72, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (506, 72, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (507, 72, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (508, 72, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (509, 72, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (510, 73, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (511, 73, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (512, 73, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (513, 73, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (514, 73, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (515, 74, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (516, 74, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (517, 74, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (518, 74, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (519, 74, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (520, 75, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (521, 75, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (522, 75, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (523, 75, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (524, 75, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (525, 76, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (526, 76, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (527, 76, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (528, 76, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (529, 76, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (530, 77, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (531, 77, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (532, 77, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (533, 77, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (534, 77, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (535, 78, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (536, 78, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (537, 78, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (538, 78, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (539, 78, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (540, 79, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (541, 79, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (542, 79, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (543, 79, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (544, 79, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (545, 80, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (546, 80, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (547, 80, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (548, 80, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (549, 80, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (550, 81, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (551, 81, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (552, 81, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (553, 81, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (554, 81, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (555, 82, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (556, 82, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (557, 82, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (558, 82, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (559, 82, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (560, 83, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (561, 83, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (562, 83, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (563, 83, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (564, 83, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (565, 84, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (566, 84, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (567, 84, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (568, 84, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (569, 84, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (570, 85, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (571, 85, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (572, 85, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (573, 85, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (574, 85, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (575, 85, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (576, 85, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (577, 86, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (578, 86, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (579, 86, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (580, 86, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (581, 86, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (582, 87, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (583, 87, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (584, 88, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (585, 88, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (586, 88, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (587, 88, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (588, 88, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (589, 89, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (590, 89, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (591, 89, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (592, 89, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (593, 89, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (594, 90, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (595, 90, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (596, 90, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (597, 90, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (598, 90, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (599, 91, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (600, 91, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (601, 91, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (602, 91, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (603, 91, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (604, 92, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (605, 92, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (606, 92, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (607, 92, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (608, 92, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (609, 93, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (610, 93, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (611, 93, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (612, 93, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (613, 93, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (614, 94, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (615, 94, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (616, 94, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (617, 94, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (618, 94, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (619, 95, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (620, 95, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (621, 95, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (622, 95, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (623, 95, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (624, 96, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (625, 96, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (626, 96, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (627, 96, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (628, 96, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (629, 97, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (630, 97, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (631, 97, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (632, 97, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (633, 97, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (634, 98, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (635, 98, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (636, 98, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (637, 98, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (638, 98, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (639, 99, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (640, 99, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (641, 99, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (642, 99, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (643, 99, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (644, 100, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (645, 100, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (646, 100, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (647, 100, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (648, 100, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (649, 101, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (650, 101, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (651, 101, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (652, 101, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (653, 101, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (654, 102, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (655, 102, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (656, 102, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (657, 102, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (658, 102, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (659, 103, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (660, 103, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (661, 103, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (662, 103, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (663, 103, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (664, 104, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (665, 104, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (666, 104, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (667, 104, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (668, 104, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (669, 105, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (670, 105, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (671, 105, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (672, 105, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (673, 105, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (674, 106, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (675, 106, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (676, 106, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (677, 106, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (678, 106, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (679, 107, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (680, 107, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (681, 107, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (682, 107, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (683, 107, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (684, 108, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (685, 108, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (686, 108, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (687, 108, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (688, 108, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (689, 109, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (690, 109, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (691, 109, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (692, 109, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (693, 109, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (694, 110, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (695, 110, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (696, 110, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (697, 110, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (698, 110, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (699, 111, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (700, 111, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (701, 111, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (702, 111, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (703, 111, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (704, 112, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (705, 112, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (706, 112, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (707, 112, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (708, 112, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (709, 113, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (710, 113, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (711, 113, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (712, 113, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (713, 113, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (714, 114, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (715, 114, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (716, 114, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (717, 114, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (718, 114, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (719, 115, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (720, 115, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (721, 115, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (722, 115, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (723, 115, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (724, 116, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (725, 116, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (726, 116, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (727, 116, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (728, 116, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (729, 117, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (730, 117, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (731, 117, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (732, 117, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (733, 117, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (734, 118, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (735, 118, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (736, 118, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (737, 118, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (738, 118, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (739, 119, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (740, 119, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (741, 119, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (742, 119, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (743, 119, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (744, 120, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (745, 120, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (746, 120, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (747, 120, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (748, 120, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (749, 121, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (750, 121, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (751, 121, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (752, 121, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (753, 121, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (754, 122, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (755, 122, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (756, 122, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (757, 122, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (758, 122, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (759, 123, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (760, 123, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (761, 123, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (762, 123, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (763, 123, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (764, 124, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (765, 124, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (766, 124, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (767, 124, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (768, 124, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (769, 125, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (770, 125, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (771, 125, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (772, 125, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (773, 125, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (774, 126, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (775, 126, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (776, 126, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (777, 126, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (778, 126, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (779, 127, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (780, 127, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (781, 127, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (782, 127, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (783, 127, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (784, 128, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (785, 128, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (786, 128, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (787, 128, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (788, 128, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (789, 129, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (790, 129, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (791, 129, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (792, 129, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (793, 129, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (794, 130, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (795, 130, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (796, 130, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (797, 130, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (798, 130, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (799, 131, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (800, 131, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (801, 131, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (802, 131, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (803, 131, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (804, 132, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (805, 132, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (806, 132, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (807, 132, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (808, 132, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (809, 133, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (810, 133, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (811, 133, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (812, 133, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (813, 133, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (814, 134, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (815, 134, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (816, 134, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (817, 134, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (818, 134, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (819, 135, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (820, 135, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (821, 135, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (822, 135, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (823, 135, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (824, 136, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (825, 136, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (826, 136, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (827, 136, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (828, 136, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (829, 137, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (830, 137, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (831, 137, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (832, 137, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (833, 137, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (834, 138, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (835, 138, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (836, 138, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (837, 138, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (838, 138, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (839, 139, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (840, 139, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (841, 139, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (842, 139, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (843, 139, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (844, 140, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (845, 140, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (846, 140, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (847, 140, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (848, 140, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (849, 141, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (850, 141, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (851, 141, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (852, 141, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (853, 141, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (854, 142, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (855, 142, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (856, 142, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (857, 142, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (858, 142, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (859, 143, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (860, 143, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (861, 143, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (862, 143, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (863, 143, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (864, 144, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (865, 144, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (866, 144, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (867, 144, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (868, 144, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (869, 145, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (870, 145, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (871, 145, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (872, 145, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (873, 145, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (874, 146, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (875, 146, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (876, 146, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (877, 146, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (878, 146, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (879, 147, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (880, 147, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (881, 147, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (882, 147, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (883, 147, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (884, 148, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (885, 148, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (886, 148, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (887, 148, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (888, 148, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (889, 149, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (890, 149, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (891, 149, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (892, 149, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (893, 149, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (894, 150, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (895, 150, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (896, 150, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (897, 150, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (898, 150, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (899, 151, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (900, 151, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (901, 151, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (902, 151, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (903, 151, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (904, 152, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (905, 152, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (906, 152, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (907, 152, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (908, 152, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (909, 153, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (910, 153, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (911, 153, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (912, 153, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (913, 153, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (914, 154, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (915, 154, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (916, 154, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (917, 154, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (918, 154, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (919, 155, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (920, 155, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (921, 155, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (922, 155, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (923, 155, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (924, 156, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (925, 156, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (926, 156, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (927, 156, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (928, 156, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (929, 157, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (930, 157, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (931, 157, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (932, 157, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (933, 157, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (934, 158, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (935, 158, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (936, 158, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (937, 158, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (938, 158, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (939, 159, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (940, 159, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (941, 159, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (942, 159, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (943, 159, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (944, 160, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (945, 160, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (946, 160, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (947, 160, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (948, 160, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (949, 161, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (950, 161, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (951, 161, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (952, 161, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (953, 161, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (954, 162, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (955, 162, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (956, 162, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (957, 162, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (958, 162, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (959, 163, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (960, 163, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (961, 163, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (962, 163, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (963, 163, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (964, 164, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (965, 164, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (966, 164, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (967, 164, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (968, 164, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (969, 165, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (970, 165, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (971, 165, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (972, 165, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (973, 165, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (974, 166, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (975, 166, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (976, 166, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (977, 166, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (978, 166, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (979, 167, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (980, 167, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (981, 167, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (982, 167, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (983, 167, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (984, 168, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (985, 168, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (986, 168, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (987, 168, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (988, 168, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (989, 169, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (990, 169, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (991, 169, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (992, 169, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (993, 169, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (994, 170, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (995, 170, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (996, 170, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (997, 170, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (998, 170, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (999, 171, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1000, 171, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1001, 171, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1002, 171, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1003, 171, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1004, 172, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1005, 172, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1006, 172, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1007, 172, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1008, 172, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1009, 173, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1010, 173, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1011, 173, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1012, 173, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1013, 173, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1014, 174, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1015, 174, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1016, 174, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1017, 174, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1018, 174, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1019, 175, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1020, 175, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1021, 175, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1022, 175, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1023, 175, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1024, 176, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1025, 176, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1026, 176, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1027, 176, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1028, 176, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1029, 177, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1030, 177, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1031, 177, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1032, 177, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1033, 177, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1034, 178, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1035, 178, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1036, 178, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1037, 178, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1038, 178, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1039, 179, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1040, 179, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1041, 179, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1042, 179, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1043, 179, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1044, 180, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1045, 180, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1046, 180, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1047, 180, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1048, 180, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1049, 181, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1050, 181, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1051, 181, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1052, 181, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1053, 181, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1054, 182, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1055, 182, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1056, 182, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1057, 182, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1058, 182, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1059, 183, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1060, 183, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1061, 183, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1062, 183, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1063, 183, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1064, 184, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1065, 184, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1066, 184, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1067, 184, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1068, 184, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1069, 185, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1070, 185, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1071, 185, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1072, 185, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1073, 185, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1074, 186, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1075, 186, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1076, 186, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1077, 186, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1078, 186, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1079, 187, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1080, 187, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1081, 187, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1082, 187, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1083, 187, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1084, 188, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1085, 188, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1086, 188, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1087, 188, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1088, 188, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1089, 189, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1090, 189, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1091, 189, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1092, 189, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1093, 189, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1094, 190, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1095, 190, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1096, 190, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1097, 190, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1098, 190, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1099, 191, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1100, 191, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1101, 191, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1102, 191, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1103, 191, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1104, 192, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1105, 192, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1106, 192, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1107, 192, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1108, 192, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1109, 193, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1110, 193, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1111, 193, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1112, 193, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1113, 193, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1114, 194, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1115, 194, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1116, 194, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1117, 194, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1118, 194, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1119, 195, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1120, 195, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1121, 195, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1122, 195, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1123, 195, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1124, 196, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1125, 196, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1126, 196, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1127, 196, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1128, 196, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1129, 197, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1130, 197, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1131, 197, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1132, 197, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1133, 197, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1134, 198, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1135, 198, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1136, 198, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1137, 198, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1138, 198, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1139, 199, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1140, 199, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1141, 199, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1142, 199, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1143, 199, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1144, 200, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1145, 200, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1146, 200, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1147, 200, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1148, 200, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1149, 201, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1150, 201, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1151, 201, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1152, 201, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1153, 201, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1154, 202, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1155, 202, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1156, 202, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1157, 202, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1158, 202, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1159, 203, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1160, 203, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1161, 203, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1162, 203, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1163, 203, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1164, 204, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1165, 204, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1166, 204, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1167, 204, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1168, 204, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1169, 205, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1170, 205, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1171, 205, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1172, 205, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1173, 205, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1174, 206, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1175, 206, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1176, 206, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1177, 206, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1178, 206, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1179, 207, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1180, 207, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1181, 207, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1182, 207, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1183, 207, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1184, 208, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1185, 208, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1186, 208, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1187, 208, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1188, 208, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1189, 209, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1190, 209, 1, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1191, 209, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1192, 209, 1, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1193, 209, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1194, 209, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1195, 209, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1196, 209, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1197, 84, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1198, 210, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1199, 210, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1200, 210, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1201, 210, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1202, 210, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1203, 210, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1204, 210, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1205, 210, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1206, 211, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1207, 211, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1208, 211, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1209, 211, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1210, 211, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1211, 211, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1212, 211, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1213, 211, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1214, 212, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1215, 212, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1216, 212, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1217, 212, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1218, 212, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1219, 212, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1220, 212, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1221, 212, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1222, 213, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1223, 213, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1224, 213, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1225, 213, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1226, 213, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1227, 213, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1228, 213, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1229, 213, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1230, 214, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1231, 214, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1232, 214, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1233, 214, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1234, 214, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1235, 214, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1236, 214, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1237, 214, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1238, 215, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1239, 215, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1240, 215, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1241, 215, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1242, 215, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1243, 215, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1244, 215, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1245, 215, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1246, 216, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1247, 216, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1248, 216, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1249, 216, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1250, 216, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1251, 216, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1252, 216, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1253, 216, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1254, 217, 3, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1255, 217, 3, 4, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1256, 217, 3, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1257, 217, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1258, 217, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1259, 217, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1260, 217, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1261, 217, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1262, 218, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1263, 218, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1264, 218, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1265, 218, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1266, 218, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1267, 219, 3, 3, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1268, 219, 3, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1269, 219, 3, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1270, 219, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1271, 219, 3, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1272, NULL, 3, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1273, 211, 2, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1274, 67, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1275, 68, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1276, 220, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1277, 220, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1278, 220, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1279, 220, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1280, 220, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1281, 220, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1282, 221, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1283, 221, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1284, 221, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1285, 221, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1286, 221, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1287, 221, 1, 2, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1288, 222, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1289, 222, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1290, 222, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1291, 222, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1292, 222, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1293, 223, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1294, 223, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1295, 223, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1296, 223, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1297, 223, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1298, 224, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1299, 224, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1300, 224, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1301, 224, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1302, 224, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1303, 225, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1304, 225, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1305, 225, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1306, 225, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1307, 225, 1, 9, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1308, 226, 1, 5, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1309, 226, 1, 6, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1310, 226, 1, 7, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1311, 226, 1, 8, 0, 0, true);
INSERT INTO modelvalidations (id, modelvariable_id, modelsection_id, modeltype_id, min, max, actif) VALUES (1312, 226, 1, 9, 0, 0, true);


--
-- Name: modelvalidations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modelvalidations_id_seq', 1313, true);


--
-- Data for Name: modelvariables; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modelvariables (id, name, description, created, modified) VALUES (1, 'nom_collectivite', 'Nom de la collectivité', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (2, 'adresse_collectivite', 'Adresse de la collectivité', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (3, 'cp_collectivite', 'Code postal de la collectivité', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (4, 'ville_collectivite', 'Ville de la collectivité', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (5, 'telephone_collectivite', 'Numéro de téléphone de la collectivité', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (6, 'date_jour_courant', 'Date du jour courant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (7, 'objet_projet', 'Libellé du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (8, 'titre_projet', 'Titre du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (9, 'position_projet', 'Position du projet dans l''ordre du jour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (10, 'identifiant_projet', 'Identifiant unique associé au projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (11, 'service_emetteur', 'Service émetteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (12, 'service_avec_hierarchie', 'Service émetteur avec hiérarchie', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (13, 'theme_projet', 'Thème du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (14, 'texte_projet', 'Texte de projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (15, 'note_synthese', 'Note de synthèse du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (16, 'texte_deliberation', 'Texte de délibération du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (17, 'etat_projet', '-1 (refusé), 0 (en cours de rédaction), 1 (dans un circuit), 2 (validé), 3 (voté pour), 4 (voté contre), 5 (envoyé au ctrl de légalité)', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (18, 'salutation_rapporteur', 'Civilité du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (19, 'prenom_rapporteur', 'Prénom du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (20, 'nom_rapporteur', 'Nom du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (21, 'titre_rapporteur', 'Titre du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (22, 'email_rapporteur', 'Adresse mail du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (23, 'telmobile_rapporteur', 'Numéro de portable du rapporteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (24, 'telfixe_rapporteur', 'Numéro de téléphone fixe du rapporteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (25, 'date_naissance_rapporteur', 'Date de naissance du rapporteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (26, 'adresse1_rapporteur', 'Adresse 1 du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (27, 'adresse2_rapporteur', 'Adresse 2 du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (28, 'cp_rapporteur', 'Code postal du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (29, 'ville_rapporteur', 'ville du rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (30, 'note_rapporteur', 'Note rédigée sur le rapporteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (31, 'prenom_redacteur', 'Prénom du rédacteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (32, 'nom_redacteur', 'Nom du rédacteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (33, 'email_redacteur', 'Adresse mail du rédacteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (34, 'telmobile_redacteur', 'Numéro de téléphone du rédacteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (35, 'telfixe_redacteur', 'Numéro de téléphone du rédacteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (36, 'date_naissance_redacteur', 'Date de naissance du rédacteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (37, 'note_redacteur', 'Note rédigée sur le rédacteur du projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (38, 'identifiant_seance', 'Identifiant unique associé à la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (39, 'date_seance', 'Date de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (40, 'date_seance_lettres', 'Date de la séance en toutes lettres', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (41, 'heure_seance', 'Heure de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (42, 'hh_seance', 'Heure  /  minutes de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (43, 'mm_seance', 'Heure  /  minutes de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (44, 'type_seance', 'Type de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (45, 'nombre_seance', 'Nombre de séances auxquelles est inscrit un projet', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (46, 'texte_commentaire', 'Commentaire entré par un valideur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (47, 'Commentaires_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (48, 'avis', 'Avis attribué en commission (avec nom et date de la commission)', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (49, 'avis_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (50, 'commentaire', 'Commentaire saisi en commission', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (51, 'fichier', 'Permet d''afficher l''annexe ODT elle-même', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (52, 'nombre_annexe', 'Nombre d''annexes éditées', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (53, 'nom_fichier', 'Nom du fichier annexé', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (54, 'titre_annexe', 'Titre donné à l''annexe', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (55, 'nom_acteur', 'Nom de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (56, 'prenom_acteur', 'Prénom de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (57, 'salutation_acteur', 'Civilité de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (58, 'titre_acteur', 'Titre de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (59, 'date_naissance_acteur', 'Date de naissance de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (60, 'adresse1_acteur', 'Adresse 1 de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (61, 'adresse2_acteur', 'Adresse 2 de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (62, 'cp_acteur', 'Code postal de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (63, 'ville_acteur', 'Ville de résidence de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (64, 'email_acteur', 'Adresse mail de l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (65, 'telfixe_acteur', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (66, 'note_acteur', 'Note rédigée sur l''acteur convoqué', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (67, 'T1_theme', 'Permet une rupture par thème', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (68, 'T2_theme', 'Permet une rupture par thème', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (69, 'T3_theme', 'Permet une rupture par thème', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (70, 'debat_seance', 'Débats généraux de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (71, 'commentaire_seance', 'Commentaires de la séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (72, 'nom_secretaire', 'Nom du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (73, 'prenom_secretaire', 'Prénom du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (74, 'salutation_secretaire', 'Civilité du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (75, 'titre_secretaire', 'Titre du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (76, 'date_naissance_secretaire', 'Date de naissance du secrétaire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (77, 'adresse1_secretaire', 'Adresse 1 du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (78, 'adresse2_secretaire', 'Adresse 2 du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (79, 'cp_secretaire', 'Code postal du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (80, 'ville_secretaire', 'Ville de résidence du secrétaire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (81, 'email_secretaire', 'Adresse mail du secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (82, 'telfixe_secretaire', 'Numéro de téléphone fixe du secrétaire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (83, 'note_secretaire', 'Note rédigée sur le secrétaire de séance', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (84, 'numero_deliberation', 'Numéro attribué à la délibération', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (85, 'date_envoi_signature', 'date d''envoi à la signature', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (86, 'classification_deliberation', 'Classification matière choisie', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (87, 'date_reception', 'Date d''acquittement de la Préfecture', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (88, 'nombre_acteur_present', 'Nombre de présents', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (89, 'nombre_acteur_absent', 'Nombre d''absents', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (90, 'nombre_acteur_mandataire', 'Nombre de mandatés et mandataires', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (91, 'nombre_pour', 'Nombre d''acteurs ayant voté pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (92, 'nombre_abstention', 'Nombre d''acteurs s''étant abstenu', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (93, 'nombre_contre', 'Nombre d''acteurs ayant voté contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (94, 'nombre_sans_participation', 'Nombre d''acteurs ne participant pas au vote', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (95, 'nombre_votant', 'Nombre total de votants', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (96, 'commentaire_vote', 'Commentaires du vote', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (97, 'debat_deliberation', 'Débat de la délibération', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (98, 'nom_acteur_present', 'Nom de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (99, 'prenom_acteur_present', 'Prénom de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (100, 'salutation_acteur_present', 'Civilité de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (101, 'titre_acteur_present', 'Titre de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (102, 'date_naissance_acteur_present', 'Date de naissance de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (103, 'adresse1_acteur_present', 'Adresse 1 de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (104, 'adresse2_acteur_present', 'Adresse 2 de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (105, 'cp_acteur_present', 'Code postal de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (106, 'ville_acteur_present', 'Ville de résidence de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (107, 'email_acteur_present', 'Adresse mail de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (108, 'telfixe_acteur_present', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (109, 'telmobile_acteur_present', 'Numéro de portable de l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (110, 'note_acteur_present', 'Note rédigée sur l''acteur présent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (111, 'ActeursPresents_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (112, 'nom_acteur_absent', 'Nom de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (113, 'prenom_acteur_absent', 'Prénom de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (114, 'salutation_acteur_absent', 'Civilité de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (115, 'titre_acteur_absent', 'Titre de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (116, 'date_naissance_acteur_absent', 'Date de naissance de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (117, 'adresse1_acteur_absent', 'Adresse 1 de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (118, 'adresse2_acteur_absent', 'Adresse 2 de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (119, 'cp_acteur_absent', 'Code postal de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (120, 'ville_acteur_absent', 'Ville de résidence de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (121, 'email_acteur_absent', 'Adresse mail de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (122, 'telfixe_acteur_absent', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (123, 'telmobile_acteur_absent', 'Numéro de portable de l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (124, 'note_acteur_absent', 'Note rédigée sur l''acteur absent', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (125, 'ActeursAbsents_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (126, 'nom_acteur_mandataire', 'Nom de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (127, 'prenom_acteur_mandataire', 'Prénom de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (128, 'salutation_acteur_mandataire', 'Civilité de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (129, 'titre_acteur_mandataire', 'Titre de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (130, 'date_naissance_acteur_mandataire', 'Date de naissance de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (131, 'adresse1_acteur_mandataire', 'Adresse 1 de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (132, 'adresse2_acteur_mandataire', 'Adresse 2 de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (133, 'cp_acteur_mandataire', 'Code postal de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (134, 'ville_acteur_mandataire', 'Ville de résidence de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (135, 'email_acteur_mandataire', 'Adresse mail de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (136, 'telfixe_acteur_mandataire', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (137, 'telmobile_acteur_mandataire', 'Numéro de portable de l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (138, 'note_acteur_mandataire', 'Note rédigée sur l''acteur mandaté', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (139, 'nom_acteur_mandate', 'Nom de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (140, 'prenom_acteur_mandate', 'Prénom de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (141, 'salutation_acteur_mandate', 'Civilité de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (142, 'titre_acteur_mandate', 'Titre de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (143, 'date_naissance_acteur_mandate', 'Date de naissance de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (144, 'adresse1_acteur_mandate', 'Adresse 1 de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (145, 'adresse2_acteur_mandate', 'Adresse 2 de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (146, 'cp_acteur_mandate', 'Code postal de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (147, 'ville_acteur_mandate', 'Ville de résidence de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (148, 'email_acteur_mandate', 'Adresse mail de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (149, 'telfixe_acteur_mandate', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (150, 'telmobile_acteur_mandate', 'Numéro de portable de l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (151, 'note_acteur_mandate', 'Note rédigée sur l''acteur mandataire', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (152, 'ActeursMandates_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (153, 'nom_acteur_contre', 'Nom de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (154, 'prenom_acteur_contre', 'Prénom de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (155, 'salutation_acteur_contre', 'Civilité de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (156, 'titre_acteur_contre', 'Titre de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (157, 'date_naissance_acteur_contre', 'Date de naissance de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (158, 'adresse1_acteur_contre', 'Adresse 1 de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (159, 'adresse2_acteur_contre', 'Adresse 2 de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (160, 'cp_acteur_contre', 'Code postal de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (161, 'ville_acteur_contre', 'Ville de résidence de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (162, 'email_acteur_contre', 'Adresse mail de l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (163, 'telfixe_acteur_contre', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (164, 'telmobile_acteur_contre', 'Numéro de portable de l''acteur contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (165, 'note_acteur_contre', 'Note rédigée sur l''acteur votant contre', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (166, 'ActeursContre_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (167, 'nom_acteur_pour', 'Nom de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (168, 'prenom_acteur_pour', 'Prénom de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (169, 'salutation_acteur_pour', 'Civilité de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (170, 'titre_acteur_pour', 'Titre de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (171, 'date_naissance_acteur_pour', 'Date de naissance de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (172, 'adresse1_acteur_pour', 'Adresse 1 de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (173, 'adresse2_acteur_pour', 'Adresse 2 de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (174, 'cp_acteur_pour', 'Code postal de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (175, 'ville_acteur_pour', 'Ville de résidence de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (176, 'email_acteur_pour', 'Adresse mail de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (177, 'telfixe_acteur_pour', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (178, 'telmobile_acteur_pour', 'Numéro de portable de l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (179, 'note_acteur_pour', 'Note rédigée sur l''acteur votant pour', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (180, 'ActeursPour_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (181, 'nom_acteur_abstention', 'Nom de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (182, 'prenom_acteur_abstention', 'Prénom de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (183, 'salutation_acteur_abstention', 'Civilité de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (184, 'titre_acteur_abstention', 'Titre de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (185, 'date_naissance_acteur_abstention', 'Date de naissance de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (186, 'adresse1_acteur_abstention', 'Adresse 1 de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (187, 'adresse2_acteur_abstention', 'Adresse 2 de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (188, 'cp_acteur_abstention', 'Code postal de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (189, 'ville_acteur_abstention', 'Ville de résidence de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (190, 'email_acteur_abstention', 'Adresse mail de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (191, 'telfixe_acteur_abstention', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (192, 'telmobile_acteur_abstention', 'Numéro de portable de l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (193, 'note_acteur_abstention', 'Note rédigée sur l''acteur s''abstenant', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (194, 'ActeursAbstention_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (195, 'nom_acteur_sans_participation', 'Nom de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (196, 'prenom_acteur_sans_participation', 'Prénom de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (197, 'salutation_acteur_sans_participation', 'Civilité de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (198, 'titre_acteur_sans_participation', 'Titre de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (199, 'date_naissance_acteur_sans_participation', 'Date de naissance de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (200, 'adresse1_acteur_sans_participation', 'Adresse 1 de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (201, 'adresse2_acteur_sans_participation', 'Adresse 2 de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (202, 'cp_acteur_sans_participation', 'Code postal de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (203, 'ville_acteur_sans_participation', 'Ville de résidence de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (204, 'email_acteur_sans_participation', 'Adresse mail de l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (205, 'telfixe_acteur_sans_participation', 'Numéro de téléphone fixe de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (206, 'telmobile_acteur_sans_participation', 'Numéro de portable de l''acteur', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (207, 'note_acteur_sans_participation', 'Note rédigée sur l''acteur ne participant pas', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (208, 'acteurs_sans_participation_separator', 'Séparateur des itérations successives', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (209, 'texte_acte', 'Texte de l''acte', '2014-02-19 11:57:39.673071', '2014-02-19 11:57:39.673071');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (210, 'identifiant_seances', 'Identifiant unique associé à la séance (Multi-séance)', '2014-03-04 14:54:24.945427', '2014-03-04 14:54:24.945427');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (211, 'date_seances', 'Date de la séance (Multi-séance)', '2014-03-04 14:54:24.995302', '2014-03-04 14:54:24.995302');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (212, 'date_seances_lettres', 'Date de la séance en toutes lettres (Multi-séance)', '2014-03-04 14:54:25.049292', '2014-03-04 14:54:25.049292');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (213, 'heure_seances', 'Heure de la séance (Multi-séance)', '2014-03-04 14:54:25.09615', '2014-03-04 14:54:25.09615');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (214, 'hh_seances', 'Heure de la séance (Multi-séance)', '2014-03-04 14:54:25.125942', '2014-03-04 14:54:25.125942');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (215, 'mm_seances', 'Minutes de la séance (Multi-séance)', '2014-03-04 14:54:25.173083', '2014-03-04 14:54:25.173083');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (216, 'type_seances', 'Type de la séance (Multi-séance)', '2014-03-04 14:54:25.274626', '2014-03-04 14:54:25.274626');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (217, 'nombre_seances', 'Nombre de séances auxquelles est inscrit un projet (Multi-séance)', '2014-03-04 14:54:25.347079', '2014-03-04 14:54:25.347079');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (218, 'debat_seances', 'Débats généraux de la séance (Multi-séance)', '2014-03-04 14:54:25.412778', '2014-03-04 14:54:25.412778');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (219, 'commentaire_seances', 'Commentaires de la séance (Multi-séance)', '2014-03-04 14:54:25.454161', '2014-03-04 14:54:25.454161');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (220, 'T4_theme', 'theme', '2014-03-14 17:23:04', '2014-03-14 17:23:04');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (221, 'T5_theme', 'Permet une rupture par thème', '2014-03-17 09:48:43.084496', '2014-03-17 09:48:43.084496');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (222, 'T6_theme', 'Permet une rupture par thème', '2014-03-17 09:48:43.146633', '2014-03-17 09:48:43.146633');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (223, 'T7_theme', 'Permet une rupture par thème', '2014-03-17 09:48:43.199119', '2014-03-17 09:48:43.199119');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (224, 'T8_theme', 'Permet une rupture par thème', '2014-03-17 09:48:43.250099', '2014-03-17 09:48:43.250099');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (225, 'T9_theme', 'Permet une rupture par thème', '2014-03-17 09:48:43.299889', '2014-03-17 09:48:43.299889');
INSERT INTO modelvariables (id, name, description, created, modified) VALUES (226, 'T10_theme', 'Permet une rupture par thème', '2014-03-17 09:48:43.357681', '2014-03-17 09:48:43.357681');


--
-- Name: modelvariables_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modelvariables_id_seq', 227, false);


--
-- Data for Name: natures; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO natures (id, libelle, code, dua, sortfinal, communicabilite) VALUES (1, 'Délibérations', 'DE', NULL, NULL, NULL);
INSERT INTO natures (id, libelle, code, dua, sortfinal, communicabilite) VALUES (2, 'Arrêtés Réglementaires', 'AR', NULL, NULL, NULL);
INSERT INTO natures (id, libelle, code, dua, sortfinal, communicabilite) VALUES (3, 'Arrêtés Individuels', 'AI', NULL, NULL, NULL);
INSERT INTO natures (id, libelle, code, dua, sortfinal, communicabilite) VALUES (4, 'Contrats et conventions', 'CC', NULL, NULL, NULL);
INSERT INTO natures (id, libelle, code, dua, sortfinal, communicabilite) VALUES (5, 'Autres', 'AU', NULL, NULL, NULL);


--
-- Name: natures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('natures_id_seq', 6, false);


--
-- Data for Name: nomenclatures; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: nomenclatures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nomenclatures_id_seq', 1, false);


--
-- Data for Name: profils; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO profils (id, parent_id, libelle, actif, created, modified) VALUES (1, 0, 'Administrateur', true, '2012-11-16 14:55:13', '2012-11-16 14:55:13');


--
-- Name: profils_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('profils_id_seq', 2, true);


--
-- Data for Name: seances; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: seances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('seances_id_seq', 1, true);


--
-- Data for Name: sequences; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO sequences (id, nom, commentaire, num_sequence, created, modified) VALUES (1, 'Arrêtés', '', 0, '2014-03-14 16:33:08', '2014-03-14 16:33:08');


--
-- Name: sequences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('sequences_id_seq', 2, true);


--
-- Data for Name: services; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO services (id, parent_id, "order", libelle, circuit_defaut_id, actif, created, modified, lft, rght) VALUES (1, 0, '', 'Informatique', 0, true, '2012-11-16 14:54:44', '2012-11-16 14:54:44', 1, 2);


--
-- Name: services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('services_id_seq', 2, true);


--
-- Data for Name: tdt_messages; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: tdt_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('tdt_messages_id_seq', 1, false);


--
-- Data for Name: themes; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO themes (id, parent_id, "order", libelle, actif, created, modified, lft, rght) VALUES (1, NULL, '', 'Défaut', true, '2012-11-16 14:54:57', '2012-11-16 14:54:57', 1, 2);


--
-- Name: themes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('themes_id_seq', 2, true);


--
-- Data for Name: traitements; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: traitements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('traitements_id_seq', 1, false);


--
-- Data for Name: typeactes; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO typeactes (id, libelle, modeleprojet_id, modelefinal_id, nature_id, compteur_id, created, modified, gabarit_projet, gabarit_synthese, gabarit_acte, teletransmettre, gabarit_acte_name, gabarit_projet_name, gabarit_synthese_name) VALUES (1, 'Délibération', 1, 1, 1, 1, '2014-03-14', '2014-03-14', NULL, NULL, NULL, false, NULL, NULL, NULL);


--
-- Name: typeactes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeactes_id_seq', 2, true);


--
-- Data for Name: typeacteurs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: typeacteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeacteurs_id_seq', 1, true);


--
-- Data for Name: typeseances; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: typeseances_acteurs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: typeseances_acteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_acteurs_id_seq', 1, false);


--
-- Name: typeseances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_id_seq', 1, true);


--
-- Name: typeseances_natures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_natures_id_seq', 1, true);


--
-- Data for Name: typeseances_typeactes; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: typeseances_typeacteurs; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: typeseances_typeacteurs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('typeseances_typeacteurs_id_seq', 1, true);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO users (id, profil_id, statut, login, note, circuit_defaut_id, password, nom, prenom, email, telfixe, telmobile, date_naissance, accept_notif, mail_refus, mail_traitement, mail_insertion, "position", created, modified, mail_modif_projet_cree, mail_modif_projet_valide, mail_retard_validation) VALUES (1, 1, 0, 'admin', '', NULL, '21232f297a57a5a743894a0e4a801fc3', 'Administrateur', 'admin', '', '', '', NULL, false, false, false, false, NULL, '2012-11-16 14:57:03', '2014-03-14 16:54:18', false, false, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_id_seq', 2, true);


--
-- Data for Name: users_services; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO users_services (id, user_id, service_id) VALUES (1, 1, 1);


--
-- Name: users_services_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_services_id_seq', 2, true);


--
-- Data for Name: votes; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: votes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('votes_id_seq', 1, true);


--
-- Data for Name: wkf_circuits; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: wkf_circuits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_circuits_id_seq', 1, true);


--
-- Data for Name: wkf_compositions; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: wkf_compositions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_compositions_id_seq', 1, true);


--
-- Data for Name: wkf_etapes; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: wkf_etapes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_etapes_id_seq', 1, true);


--
-- Data for Name: wkf_signatures; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: wkf_signatures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_signatures_id_seq', 1, false);


--
-- Data for Name: wkf_traitements; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: wkf_traitements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_traitements_id_seq', 1, true);


--
-- Data for Name: wkf_visas; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: wkf_visas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('wkf_visas_id_seq', 1, true);


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
-- PostgreSQL database dump complete
--

