<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses('Traitement', 'Cakeflow.Model');

/**
* Classe DeliberationsTest.
*
* @package app.Test.Case.Controller
* 
*/
class DeliberationsTest extends CakeTestCase {
       
    // Les fixtures de plugin localisé dans /app/Plugin/Cakeflow/Test/Fixture/
    public $fixtures = array('plugin.cakeflow.traitement');
    public $Circuit;
    
    public function testaddIntoCircuit() {
        // ClassRegistry dit au model d'utiliser la connexion à la base de données test
        $this->Circuit = ClassRegistry::init('Cakeflow.traitement');

        // faire des tests utiles ici
        $this->assertTrue(is_object($this->Circuit));
    }
}

class DeliberationsControllerTest extends CakeAppControllerTestCase {

        public function testFunction() {

        }
        
        public function testSomething() {
        // ClassRegistry dit au model d'utiliser la connection à la base de données test
        $this->addIntoCircuit = ClassRegistry::init('Blog.BlogPost');

        // faire des tests utiles ici
        $this->assertTrue(is_object($this->BlogPost));
    }

}




?>