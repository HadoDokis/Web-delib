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

class ServiceFixture extends CakeTestFixture {

        var $import = array( 'model' => 'Service', 'records' => false);
        
        public $records;


        /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
                array(
                        'id' => 1,
                        'parent_id' => 0,
                        'order' => 'A',
                        'libelle' => 'Informatique',
                        'circuit_defaut_id' => 1,
                        'actif' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'lft'=> 1,
                        'rght'=>6,
                ),
                array(
                        'id' => 2,
                        'parent_id' => 1,
                        'order' => 'AA',
                        'libelle' => 'Développement',
                        'circuit_defaut_id' => 1,
                        'actif' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'lft'=>2,
                        'rght'=>3,
                ),
                array(
                        'id' => 3,
                        'parent_id' => 1,
                        'order' => 'AB',
                        'libelle' => 'Infrastructure',
                        'circuit_defaut_id' => 1,
                        'actif' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'lft'=>4,
                        'rght'=>5,
                ),
                array(
                        'id' => 4,
                        'parent_id' => 0,
                        'order' => 'B',
                        'libelle' => 'Ressource',
                        'circuit_defaut_id' => 2,
                        'actif' => true,
                        'created' => date('Y-m-d H:i:s'),
                        'modified' => date('Y-m-d H:i:s'),
                        'lft'=>7,
                        'rght'=>8,
                ),
        );
        
        parent::init();
    }
}
