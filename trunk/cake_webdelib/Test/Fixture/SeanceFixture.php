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


class SeanceFixture extends CakeTestFixture {
    
    public $import = array('model' => 'Seance', 'records' => false);
        
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
		array(
			'id' => '1',
                        'type_id' => '1',
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'date' => '2015-01-28 18:30:00',
                        'traitee' => '0',
                        'commentaire' => null,
                        'secretaire_id' => null,
                        'debat_global' => null,
                        'debat_global_name' => null,
                        'debat_global_size' => null,
                        'debat_global_type' => null,
                        'pv_figes' => null,
                        'pv_sommaire' => null,
                        'pv_complet' => null,
		)
	);
        parent::init();
    }
}

?>