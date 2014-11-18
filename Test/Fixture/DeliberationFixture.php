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

class DeliberationFixture extends CakeTestFixture 
{
    /**
    * On importe la définition de la table, pas les enregistrements.
    *
    * @var array
    */
    //public $import = 'Deliberation';
    //var $import = array(  'records' => false);
    var $import = array( 'model' => 'Deliberation', 'records' => false);
    //var $import = array( 'table' => 'deliberations', 'connection' => 'default', 'records' => true);
    
    public $records;

    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
            array( 'id' =>1, 
                   'typeacte_id'=>1,
                   'parent_id' => NULL,
                   'objet' => 'Test connecteur sebastien',
                   'objet_delib' => 'Test connecteur sebastien',
                   'etat'=>1,
                   'theme_id'=>1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>2, 
                   'typeacte_id'=>1,
                   'parent_id' => NULL,
                   'objet' => 'Test connecteur sebastien',
                   'objet_delib' => 'Test connecteur sebastien',
                   'etat'=>1,
                   'theme_id'=>1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>3, 
                   'typeacte_id'=>1,
                   'parent_id' => NULL,
                   'objet' => 'Test connecteur sebastien',
                   'objet_delib' => 'Test connecteur sebastien',
                   'etat'=>1,
                   'theme_id'=>1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>4, 
                   'typeacte_id'=>1,
                   'parent_id' => 3,
                   'objet' => 'Test connecteur sebastien',
                   'objet_delib' => 'Test connecteur sebastien',
                   'etat'=>1,
                   'theme_id'=>1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>5, 
                   'typeacte_id'=>1,
                   'parent_id' => 3,
                   'objet' => 'Test connecteur sebastien',
                   'objet_delib' => 'Test connecteur sebastien',
                   'etat'=>1,
                   'theme_id'=>1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>6, 
                   'typeacte_id'=>1,
                   'parent_id' => NULL,
                   'objet' => 'Test connecteur sebastien',
                   'objet_delib' => 'Test connecteur sebastien',
                   'etat'=>1,
                   'theme_id'=>1,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            /*array( 'id' =>7, 'parent_id' => NULL, 'etat'=>1),
            array( 'id' =>8, 'parent_id' => NULL, 'etat'=>1),
            array( 'id' =>9, 'parent_id' => NULL, 'etat'=>1),
            array( 'id' =>10, 'parent_id' => NULL, 'etat'=>1),
            array( 'id' =>99100, 'parent_id' => NULL, 'etat'=>1),
            array( 'id' =>99101, 'parent_id' => NULL, 'etat'=>1)*/
        );
        
        parent::init();
    }
}

?>