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

class AnnexFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
        public $import = array( 'table' => 'annexes','model' => 'Annex', 'records' => false);
    
        public $fields = array(
            'id' => array('type' => 'integer', 'key' => 'primary'),
            'model' => array('type' => 'string'),
            'foreign_key' => array('type' => 'integer'),
            'joindre_ctrl_legalite' => array('type' => 'bool'),
            'titre' => array('type' => 'string'),
            'filename' => array('type' => 'string'),
            'filetype' => array('type' => 'string'),
            'size' => array('type' => 'integer'),
            'data' => array('type' => 'binary'),
            'data_pdf' => array('type' => 'binary'),
            'created' => array('type' => 'string'),
            'modified' => array('type' => 'string'),
            'joindre_fusion' => array('type' => 'integer'),
            'edition_data' => array('type' => 'binary'),
            'edition_data_typemime' => array('type' => 'string')
        );
        
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
		array(
			'id' => 215,
			'model' => 'Projet',
			'foreign_key' => 341,
			'joindre_ctrl_legalite' => true,
			'titre' => 'Annexe 1',
			'filename' => 'Commande_IN140006_259863.pdf',
			'filetype' => 'application/pdf',
			'size' => 501044,
			'data' => '',
			'data_pdf' => null,
			'created' => date('Y-m-d H:i:s'),
			'modified' => date('Y-m-d H:i:s'),
			'joindre_fusion' => 1,
			'edition_data' => null,
                        'edition_data_typemime' => null
		),/*
               array(
			'id' => 216,
			'model' => 'Projet',
			'foreign_key' => 341,
			'joindre_ctrl_legalite' => true,
			'titre' => 'Annexe 1',
			'filename' => 'Commande_IN140006_259863.pdf',
			'filetype' => 'application/pdf',
			'size' => 501044,
			'data' => file_get_contents(dirname(__FILE__).DS.'..'.DS.'data'.DS.'AnnexFixture.pdf'),
			'data_pdf' => null,
			'created' => '2014-01-28 14:44:54',
			'modified' => '2014-01-28 14:44:54',
			'joindre_fusion' => 0,
			'edition_data' => null,
                        'edition_data_typemime' => null
		),*/
	);
        parent::init();
    }
}
