<?php
/**
* Code source de la classe CompteurTest.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses( 'Compteur', 'Model' );

/**
* Classe CompteurTest.
*
* @package app.Test.Case.Model
* 
*/

class CompteurTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array('app.compteur','app.sequence');

    public function setUp() {
        parent::setUp();
        $this->Compteur = ClassRegistry::init('Compteur');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset( $this->Compteur );
    }

    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function test_genereCompteur(){
        
        $result=$this->Compteur->genereCompteur(1);
        
        $expected = date('Y').'_'.date('m').'_001';
                
        $this->assertEquals($result, $expected, var_export($result, true));
    }

}

?>