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
    public function testGetDocumentTypes(){
//        debug ($this->PastellComponent->getDocumentTypes());
        return ($this->PastellComponent->getDocumentTypes());
    }

    /**
     * Test getInfosType()
     * @return void
     */
    public function testGetDocumentTypeInfos(){
//        debug ($this->PastellComponent->getDocumentTypeInfos('actes-generique'));
        return ($this->PastellComponent->getDocumentTypeInfos('actes-generique'));
    }

    /**
     * Test getInfosType()
     * @return void
     */
    public function testGetDocumentTypeActions(){
        debug ($this->PastellComponent->getDocumentTypeActions('actes-generique'));
        return ($this->PastellComponent->getDocumentTypeActions('actes-generique'));
    }

    /**
     * Test listEntities()
     * @return void
     */
    public function testListEntities(){
//        debug ($this->PastellComponent->listEntities());
        return ($this->PastellComponent->listEntities(true));
    }

    /**
     * Test listEntities()
     * @return void
     */
    public function testListDocuments(){
//        debug ($this->PastellComponent->listDocuments(48,'actes-generique'));
        return ($this->PastellComponent->listDocuments(48,'actes-generique'));
    }

    /**
     * Test rechercheDocument()
     * @return void
     */
    public function testRechercheDocument(){
        $options = array(
            'id_e' => 48,
            'type' => 'actes-generique',
            'search' => 'test sans sources'
        );

//        debug ($this->PastellComponent->rechercheDocument($options));
        return ($this->PastellComponent->rechercheDocument($options));
    }

    /**
     * Test detailDocument()
     * @return void
     */
    public function testDetailDocument(){
        debug ($this->PastellComponent->detailDocument(3,'Hb63sCE'));
        debug ($this->PastellComponent->detailDocument(3,'FjZWMlC'));

//        return ($this->PastellComponent->detailDocument(48,'elRx6ID'));
    }

    /**
     * FIXME
     * Test detailDocuments()
     * @return void
     */
    public function testDetailDocuments(){
//        debug ($this->PastellComponent->detailDocuments(48,array('id_d: elRx6ID', 'id_d: dfsfsdf')));
//        return ($this->PastellComponent->detailDocuments(48,array('elRx6ID')));
    }

    /**
     * OK en v1.3
     * Test createDocument()
     * @return void
     */
    public function testCreateDocument(){
//        debug ($this->PastellComponent->createDocument(48,'actes-generique'));
//        return ($this->PastellComponent->createDocument(48,'actes-generique'));
    }

    /**
     * FIXME
     * Test getInfosField()
     * @return void
     */
    public function testGetInfosField(){
        debug ($this->PastellComponent->getInfosField(3,'L4iaPx6', null));
//        return ($this->PastellComponent->getInfosField(48,'actes-generique'));
    }


}
