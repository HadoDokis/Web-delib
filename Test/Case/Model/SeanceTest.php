<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses( 'Seance', 'Model' );

/**
* Classe Histochoixcer93Test.
*
* @package app.Test.Case.Model
* 
*/

class SeanceTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array('app.deliberationseance');

    public function setUp() {
        parent::setUp();
        $this->Seance = ClassRegistry::init('Seance');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset( $this->Seance );
    }

    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function deleteSeanceTest(){
        
        $this->assertEquals('1', '1','test OK');
    }
    /*public function testPublished() {
        $result = $this->Article->published(array('id', 'title'));
        $expected = array(
            array('Article' => array('id' => 1, 'title' => 'First Article')),
            array('Article' => array('id' => 2, 'title' => 'Second Article')),
            array('Article' => array('id' => 3, 'title' => 'Third Article'))
        );

        $this->assertEquals($expected, $result);
    }*/

}

?>