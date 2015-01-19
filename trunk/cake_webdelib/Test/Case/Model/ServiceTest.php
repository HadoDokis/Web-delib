<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses( 'Service', 'Model' );
App::uses('CakeTime', 'Utility');

/**
* Classe SeanceTest.
*
* @package app.Test.Case.Model
* 
*/

class ServiceTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array(
                            'app.Service',
                            'app.UserService',
                            'app.Profil',
                            'app.Historique',
                            'app.CircuitUser',
                            //'app.ActeurService',
                            'plugin.Cakeflow.Circuit',
                            'plugin.Cakeflow.Traitement',
                            'plugin.Cakeflow.Visa',
                            'plugin.Cakeflow.Etape',
                            'plugin.Cakeflow.Composition',
                            'app.User'
                            );
    
    public $autoFixtures = false;

    public function setUp() {
        parent::setUp();
        $this->Service = ClassRegistry::init('Service');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset($this->Service);
    }
        
}