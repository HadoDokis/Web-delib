<?php
/**
* Code source de la classe ImportShellTest.
*
* PHP 5.3
*
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
* @author Adullact-projet <contact@adullact-projet.coop>
*/

/**
* Classe ImportShellTest.
*
* @package app.Test.Case.Console.Command
* 
*/
App::uses('AppShell', 'Console/Command');
App::uses('Infosup', 'Model');
App::uses('Infosupdef', 'Model');
App::uses('Infosuplistedef', 'Model');
App::uses('ImportShell', 'Console/Command');
App::uses('AppTools', 'Lib');

class ImportShellTest extends CakeTestCase {
    public $Infosup;
    
    public $Infosupdef;
    
    public $Infosuplistedef;
    
    public $ImportShell;
    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array(
                        'app.Infosup',
                        'app.Infosupdef',
                        'app.Infosuplistedef');  
    
    public function setUp() {
        parent::setUp();
        $this->ImportShell = new ImportShell();
        $this->ImportShell->initialize(); 
        $this->Infosup = ClassRegistry::init('Infosup');
        $this->Infosupdef = ClassRegistry::init('Infosupdef');
        $this->Infosuplistedef = ClassRegistry::init('Infosuplistedef');
    }
    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        parent::tearDown();
        unset($this->Infosup);
        unset($this->Infosupdef);
        unset($this->Infosuplistedef);
    }
  
    /**
     * Test Import Informations supllémentaires liste()
     */
    public function testinfoSupListe1collone() {
        
        App::uses('File', 'Utility');
        $fileFlux = new File(AppTools::newTmpDir(TMP.'files/test').'/test_', true, 0777);
        $data="nom\nMONTPELLIER\nGRABELS";
        $fileFlux->write($data);
        $fileFlux->close();
        
        $this->ImportShell->params=array(
                                            'code'=>'axes',
                                            'file'=>$fileFlux->pwd(),
                                            'desactive'=>'true'
                                        );
       $this->ImportShell->infoSupListe();
        
        $result = $this->Infosuplistedef->find('all',array(
                        'fields'     => 'nom,ordre',
                        'conditions' => array('actif'=>true,'infosupdef_id'=>1),
                        'recursive'=>-1,
                        'order'      => array( 'ordre ASC' )));
        $expected = array(
            array('Infosuplistedef' => array('nom'=>'MONTPELLIER', 'ordre' => 1)),
            array('Infosuplistedef' => array('nom'=>'GRABELS', 'ordre' => 2)),
        );
        
        $this->assertEquals($expected, $result, var_export( $result, true));
        $fileFlux->delete();
    }
    
    /**
     * Test Import Informations supllémentaires liste()
     */
    public function testinfoSupListe2collone() {
        
        App::uses('File', 'Utility');
        $fileFlux = new File(AppTools::newTmpDir(TMP.'files/test').'/test_', true, 0777);
        $data="nom;actif\nMONTPELLIER;1\nGRABELS;1";
        $fileFlux->write($data);
        $fileFlux->close();
        
        $this->ImportShell->params=array(
                                            'code'=>'axes',
                                            'file'=>$fileFlux->pwd(),
                                            'desactive'=>'true'
                                        );
       $this->ImportShell->infoSupListe();
        
        $result = $this->Infosuplistedef->find('all',array(
                        'fields'     => 'nom,actif,ordre',
                        'conditions' => array('actif'=>true,'infosupdef_id'=>1),
                        'recursive'=>-1,
                        'order'      => array( 'ordre ASC' )));
        $expected = array(
            array('Infosuplistedef' => array('nom'=>'MONTPELLIER', 'actif' => true,'ordre' => 1)),
            array('Infosuplistedef' => array('nom'=>'GRABELS', 'actif' => true, 'ordre' => 2)),
        );
        
        $this->assertEquals($expected, $result, var_export( $result, true));
        $fileFlux->delete();
    }
}

?>