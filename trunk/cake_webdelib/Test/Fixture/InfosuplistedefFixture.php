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
class InfosuplistedefFixture extends CakeTestFixture {
    var $import = array( 'model' => 'Infosuplistedef', 'records' => false);
    var $records;

    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
            array( 'id' =>1, 
                   'infosupdef_id'=>2,
                   'ordre' => 1,
                   'nom' => 'PALAVAS',
                   'actif' => 1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>2, 
                   'infosupdef_id'=>2,
                   'ordre' => 2,
                   'nom' => 'CARNON',
                   'actif' => 0,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>3, 
                   'infosupdef_id'=>2,
                   'ordre' => 2,
                   'nom' => 'LATTES',
                   'actif' => 1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
        );
        
        parent::init();
    }
}
?>