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

class TypeseanceFixture extends CakeTestFixture 
{
    public $import = array('model' => 'Typeseance', 'records' => false);
    
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
		array(
			'id' => 1,
			'libelle' => 'Conseil municipal',
                        'retard' => 30,
                        'action' => 0,
                        'compteur_id' => 1,
                        'modelprojet_id' => 2,
                        'modeldeliberation_id' => 3,
                        'modelconvocation_id' => 4,
                        'modelordredujour_id' => 5,
                        'modelpvsommaire_id' => 6,
                        'modelpvdetaille_id' => 7,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
		)
	);
        parent::init();
    }
}

?>