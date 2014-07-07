<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses('Shell', 'Console');
App::uses('MessageTdt', 'Model');
App::uses('TdtTask', 'Console/Command/Task');

/**
* Classe Histochoixcer93Test.
*
* @package app.Test.Case.Model
* 
*/

class TdtTaskTest extends CakeTestCase {

    public $TdtMessage;
    
    public $TdtTask;
    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array(
                        'app.Deliberation',
                        'app.TdtMessage');
    
    public function setUp() {
        parent::setUp();
        $this->TdtTask = new TdtTask();
        $this->TdtTask->initialize(); 
        $this->TdtMessage = ClassRegistry::init('TdtMessage');
    }
    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        parent::tearDown();
        unset($this->TdtTask);
        unset($this->TdtMessage);
    }

        /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testmigrationMessageTdt4201(){
        
        $this->TdtTask->migrationMessageTdt4201();
        
        $result = $this->TdtMessage->find('all',array(
                        'fields'     => array( 'TdtMessage.id', 'parent_id' ),
                        'recursive'=>-1,
                        'order'      => array( 'TdtMessage.id ASC' )));

        $expected = array(
            array('TdtMessage' => array('id' => 1, 'parent_id' => NULL)),
            array('TdtMessage' => array('id' => 2, 'parent_id' => NULL)),
            array('TdtMessage' => array('id' => 3, 'parent_id' => 1)),
            array('TdtMessage' => array('id' => 4, 'parent_id' => NULL)),
            array('TdtMessage' => array('id' => 5, 'parent_id' => 4)),
        );
         
        $this->assertEquals($expected, $result, var_export( $result, true));
    }
}

?>