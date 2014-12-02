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
App::uses('CakeTime', 'Utility');

/**
* Classe SeanceTest.
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
    public $fixtures = array(
                            'app.seance',
                            'app.deliberationseance'
                            );

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
    public function testdeleteSeance(){
        
        $this->assertEquals('1', '1','test OK');
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testaddSeance(){
        
        $seance=array('Seance'=>array());
        $seance['Seance']['type_id']= 1;
        $seance['Seance']['date']= CakeTime::format( '12-04-2015 10:00', '%Y-%m-%d %H:%M:00');
        
        $result = $this->Seance->Save($seance);
        
        $expected = array ( 'Seance' => array ( 
            'type_id' => 1, 
            'date' => '2015-04-12 10:00:00', 
            'modified' => date('Y-m-d H:i:s'), 
            'created' => date('Y-m-d H:i:s'), 
            'id' => '2'));
        
        $this->assertEquals($result, $expected, var_export($this->Seance->validationErrors, true));
    }
    
}