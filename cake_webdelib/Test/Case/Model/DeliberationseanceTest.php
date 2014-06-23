<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses( 'Deliberationseance', 'Model' );
App::uses( 'Deliberation', 'Model' );

/**
* Classe Histochoixcer93Test.
*
* @package app.Test.Case.Model
* 
*/

class DeliberationseanceTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array('app.deliberationseance',
                             'app.deliberation'  ,
                             'app.seance', 
                             /*'app.service',
                             'app.model',
                             'app.theme',
                             'app.user',
                             'app.acteur',
                             'app.typeacte',
                             'app.tdtMessage',
                             'app.historique',*/
                             /*'plugin.cakeflow.circuit',
                             'plugin.cakeflow.traitement',*/
                            );

    public function setUp() {
        parent::setUp();
        $this->Deliberationseance = ClassRegistry::init('Deliberationseance');
        $this->Deliberation = ClassRegistry::init('Deliberation');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset( $this->Deliberationseance );
    }

        /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testaddDeliberationseance(){
        $this->Deliberationseance->addDeliberationseance(3,1);
        $this->Deliberationseance->addDeliberationseance(6,1);
        $result = $this->Deliberationseance->find('all', array('conditions' => array( 'Deliberationseance.seance_id' => 1),
                        'fields'     => array( 'Deliberationseance.id', 'Deliberationseance.position' ),
                        'order'      => array( 'Deliberationseance.position ASC' )));

        $expected = array(
            array('Deliberationseance' => array('id' => 1, 'position' => '1')),
            array('Deliberationseance' => array('id' => 2, 'position' => '2')),
            array('Deliberationseance' => array('id' => 3, 'position' => '3')),
            array('Deliberationseance' => array('id' => 4, 'position' => '4')),
            array('Deliberationseance' => array('id' => 5, 'position' => '5')),
            array('Deliberationseance' => array('id' => 6, 'position' => '6'))
        );
        
        $this->assertEquals($expected, $result, var_export( $result, true));
    }
    
    /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testdeleteDeliberationseance(){
        $this->Deliberationseance->addDeliberationseance(3,1);
        $this->Deliberationseance->addDeliberationseance(6,1);
        $this->Deliberationseance->deleteDeliberationseance(3,1);
        $result = $this->Deliberationseance->find('all', array('conditions' => array( 'Deliberationseance.seance_id' => 1),
                        'fields'     => array( 'Deliberationseance.id', 'Deliberationseance.position' ),
                        'order'      => array( 'Deliberationseance.position ASC' )));

         $expected = array(
            array('Deliberationseance' => array('id' => 1, 'position' => '1')),
            array('Deliberationseance' => array('id' => 2, 'position' => '2')),
            array('Deliberationseance' => array('id' => 6, 'position' => '3'))
        );
        
        $this->assertEquals($expected, $result, var_export( $result, true));
    }  
        /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testaddFalseDeliberationseance(){
        //On crée un problème sur une délibération mal positionné
        $deliberationseance['position'] = 10;
        $deliberationseance['deliberation_id'] = 7;
        $deliberationseance['seance_id'] = 1;
        $this->Deliberationseance->create($deliberationseance);
        $this->Deliberationseance->save();
        
        $deliberation['id'] = 7;
        $deliberation['etat'] = 1;
        $deliberation['parent_id'] = NULL;
        $this->Deliberation->create($deliberation);
        $this->Deliberation->save();
        
        $this->Deliberationseance->addDeliberationseance(3,1);
        $this->Deliberationseance->addDeliberationseance(6,1);
        $result = $this->Deliberationseance->find('all', array('conditions' => array( 'Deliberationseance.seance_id' => 1),
                        'fields'     => array( 'Deliberationseance.id', 'Deliberationseance.position' ),
                        'order'      => array( 'Deliberationseance.position ASC' )));

         $expected = array(
            array('Deliberationseance' => array('id' => 1, 'position' => '1')),
            array('Deliberationseance' => array('id' => 2, 'position' => '2')),
            array('Deliberationseance' => array('id' => 3, 'position' => '3')),
            array('Deliberationseance' => array('id' => 4, 'position' => '4')),
            array('Deliberationseance' => array('id' => 5, 'position' => '5')),
            array('Deliberationseance' => array('id' => 6, 'position' => '6')),
            array('Deliberationseance' => array('id' => 7, 'position' => '7'))
        );
        
        $this->assertEquals($expected, $result, var_export( $result, true));
    }

}

?>