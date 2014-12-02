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
class InfosupdefFixture extends CakeTestFixture {
    
    var $import = array( 'model' => 'Infosupdef', 'records' => false);
    var $records;

    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
            array( 'id' =>1, 
                    'model'=> 'Deliberation',
                    'nom' => 'Axe',
                    'commentaire' => '',
                    'ordre' => 1,
                    'code' => 'axes',
                    'type' => 'listmulti',
                    'val_initiale' => '',
                    'recherche' => false,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                    'actif' => true,
                ),
            array( 'id' =>2, 
                    'model'=> 'Deliberation',
                    'nom' => 'Axe',
                    'commentaire' => '',
                    'ordre' => 1,
                    'code' => 'axes2',
                    'type' => 'listmulti',
                    'val_initiale' => '',
                    'recherche' => false,
                    'created' => date('Y-m-d H:i:s'),
                    'modified' => date('Y-m-d H:i:s'),
                    'actif' => true,
                ),
        );
        
        parent::init();
    }
}
?>