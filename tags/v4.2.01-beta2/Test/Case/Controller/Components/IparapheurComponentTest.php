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
App::uses('IparapheurComponent', 'Controller/Component');

// Un faux controller pour tester against
class TestIparapheurController extends Controller {
    public $paginate = null;
}

class IparapheurComponentTest extends CakeTestCase {
    public $IparapheurComponent = null;
    public $Controller = null;

    public function setUp() {
        parent::setUp();
        // Configurer notre component et faire semblant de tester le controller
        $Collection = new ComponentCollection();
        $this->IparapheurComponent = new IparapheurComponent($Collection);
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        $this->Controller = new TestIparapheurController($CakeRequest, $CakeResponse);
        $this->IparapheurComponent->startup($this->Controller);
    }

    /**
     * Méthode exécutée avant chaque test.
     *
     * @return void
     */
    public function tearDown() {
        parent::tearDown();
        unset($this->IparapheurComponent);
        unset($this->Controller);
    }

    /**
     * Test get Variables
     * @return void
     */
    public function testVariables() {

    }

}
