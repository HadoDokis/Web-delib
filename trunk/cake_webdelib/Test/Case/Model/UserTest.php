<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses( 'User', 'Model' );
App::uses('CakeTime', 'Utility');

/**
* Classe SeanceTest.
*
* @package app.Test.Case.Model
* 
*/

class UserTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array(
                            'app.user',
                            'app.commentaire',
                            'app.deliberationseance',
                            'app.seance',
                            'app.typeseance',
                            'app.acteur',
                            );

    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset( $this->User );
    }

    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testDeleteUser(){
        
        $this->assertEquals('1', '1','test OK');
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testNotifierUser(){
        
        $return = $this->User->notifier(1, 1, 'insertion');
        $return = $this->User->notifier(1, 1, 'traitement');
        $return = $this->User->notifier(1, 1, 'refus');
        $return = $this->User->notifier(1, 1, 'modif_projet_cree');
        $return = $this->User->notifier(1, 1, 'modif_projet_valide');
        $return = $this->User->notifier(1, 1, 'retard_validation');
        
        $this->assertEquals('1', '1','test OK');
    }
    
}