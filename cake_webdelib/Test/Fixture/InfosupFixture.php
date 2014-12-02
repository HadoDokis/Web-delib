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
class InfosupFixture extends CakeTestFixture {
    
    var $import = array( 'model' => 'Infosup', 'records' => false);
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
                    'foreign_key' => 1,
                    'infosupdef_id' => 1,
                    'text' => NULL,
                    'date' => NULL,
                    'file_name' => NULL,
                    'file_size' => NULL,
                    'file_type' => NULL,
                    'content' => NULL
                ),
        );
        
        parent::init();
    }
}
?>