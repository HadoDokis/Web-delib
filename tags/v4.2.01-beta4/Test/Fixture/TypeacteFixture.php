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

class TypeacteFixture extends CakeTestFixture 
{
    public $import = array( 'model' => 'Typeacte', 'records' => false);

    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
		array(
			'id' => 1,
			'libelle' => 'Délibération',
                        'modeleprojet_id' => 1,
                        'modelefinal_id' => 1,
                        'nature_id' => 1,
                        'compteur_id' => 1,
                        'gabarit_projet' => NULL,
                        'gabarit_synthese' => NULL,
                        'gabarit_acte' => NULL,
                        'teletransmettre' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
		)
	);
        parent::init();
    }
}
?>