<?php

/**
 * Code source de la classe DeliberationFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe DeliberationFixture ...
 *
 * @package app.Test.Fixture
 */
class DeliberationFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Deliberation',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
            'id' => 276,
            'typeacte_id' => 2,
            'circuit_id' => 39,
            'theme_id' => 25,
            'service_id' => 58,
            'redacteur_id' => 40,
            'rapporteur_id' => 21,
            'anterieure_id' => 176,
            'is_multidelib' => NULL,
            'parent_id' => NULL,
            'objet' => 'Avenant nÂ°1 au protocole d\'accord du 26 octobre 2012 avec le PLIE Mode d\'Emploi',
            'objet_delib' => 'Avenant nÂ°1 au protocole d\'accord du 26 octobre 2012 avec le PLIE Mode d\'Emploi',
            'titre' => '',
            'num_delib' => 'DEL20130627_1',
            'num_pref' => '',
            'pastell_id' => NULL,
            'tdt_id' => NULL,
            'dateAR' => NULL,
            'texte_projet' => '',
            'texte_projet_name' => '',
            'texte_projet_type' => '',
            'texte_projet_size' => 0,
            'texte_synthese' => '',
            'texte_synthese_name' => 'maquette_note.odt',
            'texte_synthese_type' => 'application/vnd.oasis.opendocument.text',
            'texte_synthese_size' => 8761,
            'deliberation' => '',
            'deliberation_name' => 'maquette_texte_deliberation.odt',
            'deliberation_type' => 'application/vnd.oasis.opendocument.text',
            'deliberation_size' => 8777,
            'date_limite' => NULL,
            'date_envoi' => '2013-05-28 16:09:18',
            'etat' => 3,
            'etat_parapheur' => NULL,
            'commentaire_refus_parapheur' => NULL,
            'etat_asalae' => NULL,
            'reporte' => false,
            'montant' => NULL,
            'debat' => NULL,
            'debat_name' => NULL,
            'debat_size' => NULL,
            'debat_type' => NULL,
            'avis' => NULL,
            'created' => '2013-05-22 17:08:07',
            'modified' => '2013-08-19 17:01:43',
            'vote_nb_oui' => 40,
            'vote_nb_non' => 2,
            'vote_nb_abstention' => 0,
            'vote_nb_retrait' => 0,
            'vote_commentaire' => '',
            'delib_pdf' => NULL,
            'bordereau' => NULL,
            'signature' => NULL,
            'signee' => NULL,
            'commission' => NULL,
            'commission_size' => NULL,
            'commission_type' => NULL,
            'commission_name' => NULL,
            'date_acte' => NULL,
            'date_envoi_signature' => NULL,
            'id_parapheur' => NULL,
        )
    );

}

?>
