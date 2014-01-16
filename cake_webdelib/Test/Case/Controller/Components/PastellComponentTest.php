<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('PastellComponent', 'Controller/Component' );

// Un faux controller pour tester against
class TestPastellController extends Controller {
    public $paginate = null;
}

class PastellComponentTest extends CakeTestCase {
    public $PastellComponent = null;
    public $Controller = null;

    public function setUp() {
        parent::setUp();
        // Configurer notre component et faire semblant de tester le controller
        $Collection = new ComponentCollection();
        $this->PastellComponent = new PastellComponent($Collection);
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestPastellController($CakeRequest, $CakeResponse);
        $this->PastellComponent->startup($this->Controller);
    }

    /**
     * Méthode exécutée avant chaque test.
     *
     * @return void
     */
    public function tearDown() {
        parent::tearDown();
        unset($this->PastellComponent);
        unset($this->Controller);
    }

    /**
     * Test getVersion()
     * @return void
     */
    public function testGetVersion(){
        return ($this->PastellComponent->getVersion());
    }

    /**
     * Test getDocumentsType()
     * @return void
     */
    public function testGetDocumentsType(){
        return ($this->PastellComponent->getDocumentsType());
    }

    /**
     * Test getInfosType()
     * @return void
     */
    public function testGetInfosType(){
        $types = $this->PastellComponent->getDocumentsType();
        debug($types);
        debug ($this->PastellComponent->getInfosType($types[0]));
    }


}
