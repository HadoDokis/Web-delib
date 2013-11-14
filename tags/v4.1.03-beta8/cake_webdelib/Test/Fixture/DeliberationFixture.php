<?php
/**
* Code source de la classe ActionFixture.
*
* PHP 5.3
*
* @package app.Test.Fixture
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/

/**
* Classe ActionFixture.
*
* @package app.Test.Fixture
*/

class DeliberationFixture extends CakeTestFixture 
{
    /**
    * On importe la définition de la table, pas les enregistrements.
    *
    * @var array
    */
    //public $import = 'Deliberation';
    var $import = array(  'records' => false);
    
    public $fields = array(
          'id' => array('type' => 'integer', 'key' => 'primary'),
          'etat' => array('type' => 'integer'),
          'parent_id' => array('type' => 'integer')
    );
    
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    var $records = array(
        array(
            'id' =>1,
//  'typeacte_id integer NOT NULL DEFAULT 1,
//  'circuit_id integer DEFAULT 0,
//  'theme_id integer NOT NULL DEFAULT 0,
//  'service_id integer NOT NULL DEFAULT 0,
//  'vote_id integer NOT NULL DEFAULT 0,
//  'redacteur_id integer NOT NULL DEFAULT 0,
//  'rapporteur_id integer DEFAULT 0,
//  'seance_id integer,
//  'position' integer,
//  'anterieure_id integer,
//  'is_multidelib boolean,
    'parent_id' => NULL,
//  'objet character varying(1000) NOT NULL,
//  'objet_delib character varying(1000),
//  'titre character varying(1000),
//  'num_delib character varying(15),
//  'num_pref character varying(100),
//  'tdt_id integer,
//  "dateAR" character varying(100),
// ' texte_projet bytea,
//  'texte_projet_name character varying(75),
// ' texte_projet_type character varying(255),
//  'texte_projet_size integer,
//  'texte_synthese bytea,
//  'texte_synthese_name character varying(75),
//  'texte_synthese_type character varying(255),
//  'texte_synthese_size integer,
//  'deliberation bytea,
//  'deliberation_name character varying(75),
//  'deliberation_type character varying(255),
//  'deliberation_size integer,
//  'date_limite date,
//  'date_envoi timestamp without time zone,
    'etat'=>1,
//  'etat_parapheur smallint,
//  'etat_asalae boolean,
//  'reporte boolean NOT NULL DEFAULT false,
//  'localisation1_id integer NOT NULL DEFAULT 0,
//  'localisation2_id integer NOT NULL DEFAULT 0,
//  'localisation3_id integer NOT NULL DEFAULT 0,
//  'montant integer,
//  'debat bytea,
//  'debat_name character varying(2555),
//  'debat_size integer,
//  'debat_type character varying(255),
//  'commission bytea,
//  'commission_size integer,
//  'commission_type character varying(255),
//  'commission_name character varying(255),
//  'avis integer,
//  'created timestamp without time zone NOT NULL,
//  'modified timestamp without time zone NOT NULL,
//  'vote_nb_oui integer,
//  'vote_nb_non integer,
//  'vote_nb_abstention integer,
//  'vote_nb_retrait integer,
//  'vote_commentaire character varying(500),
//  'delib_pdf bytea,
//  'signature bytea,
//  'signee boolean,
//  'date_acte timestamp without time zone,
//  'pastell_id integer,
//  'commentaire_refus_parapheur character varying(1000),
//  'date_envoi_signature timestamp without time zone,
//  'bordereau bytea,
//  'id_parapheur
        ),
        array(
            'id' =>2,
            'parent_id' => NULL,
            'etat'=>1,
            
        ),
        array(
            'id' =>3,
            'parent_id' => NULL,
            'etat'=>1,
        ),
        array(
            'id' =>4,
            'parent_id' => 3,
            'etat'=>1,
        ),
        array(
            'id' =>5,
            'parent_id' => 3,
            'etat'=>1,
        ),
        array(
            'id' =>6,
            'parent_id' => NULL,
            'etat'=>1,
        )
    );
}

?>