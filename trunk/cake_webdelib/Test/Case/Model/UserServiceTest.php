<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses( 'UserService', 'Model' );
App::uses('CakeTime', 'Utility');

/**
* Classe UserServiceTest.
*
* @package app.Test.Case.Model
* 
*/

class UserServiceTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array(
                            'app.Service',
                            'app.UserService',
                            'app.User'
                            );
    
    public function setUp() {
        parent::setUp();
        $this->UserService = ClassRegistry::init('UserService');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset($this->UserService);
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    
    public function testFusionSeance(){

        $this->UserService->fusion(4, 1);
        
        $result = $this->UserService->find('all', array(
                    'fields' => array('UserService.user_id','UserService.service_id'),
                    'conditions' => array('OR'=>array('service_id' => 4,'service_id' => 1)),
                    'recursive'=>-1,
                    'order'=>'id ASC'));
        
        $expected = array(
            array('UserService' => array('user_id' => 1, 'service_id' => 1)),
            array('UserService' => array('user_id' => 7, 'service_id' => 1)),
            array('UserService' => array('user_id' => 5, 'service_id' => 1)),
            array('UserService' => array('user_id' => 6, 'service_id' => 1)),
            
        );
        
        $this->assertEquals($result, $expected, var_export($this->UserService->validationErrors, true));
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testFusionSeanceEgal(){
        //false, 'Impossible de fusionner le même service'
        $this->expectException('Exception', 'Impossible de fusionner le même service');
        
        $this->UserService->fusion(1, 1);
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testFusionSeanceParent(){
        //false, 'Impossible de fusionner le même service'
        $this->expectException('Exception', 'Impossible de fusionner ce service : il possède au moins un service');
        
        $this->UserService->fusion(1, 2);
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testFusionSeanceNotExist(){
        //false, 'Impossible de fusionner le même service'
        $this->expectException('Exception', 'Invalide ids pour fusionner le service');
        
        $this->UserService->fusion(999, 1);
    }
}