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
* @package app.Test.ThemeFixture
*/

class ThemeFixture extends CakeTestFixture {

    public $import = array( 'model' => 'Theme', 'records' => false);
    
    public $records;
    
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
            array(
                        'id' => '1',
                        'parent_id' => '0',
                        'order' => 'A',
                        'libelle' => 'Défaut',
                        'actif' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'lft' => 1,
                        'rght' => 4,
                ),
                array(
                        'id' => '2',
                        'parent_id' => '1',
                        'order' => 'A1',
                        'libelle' => 'Sous - Défaut',
                        'actif' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'lft' => 2,
                        'rght' => 3,
                ),
        );
        
        parent::init();
    }
}

?>