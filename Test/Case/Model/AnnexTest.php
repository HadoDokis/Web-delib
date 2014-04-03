<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/

/**
* Classe Histochoixcer93Test.
*
* @package app.Test.Case.Model
* 
*/

class AnnexTest extends CakeTestCase {

    /**
    * Fixtures associated with this test case
    *
    * @var array
    */
    public $fixtures = array('annex');

    public function setUp() {
        parent::setUp();
         $this->Annex = ClassRegistry::init('Annex');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        unset( $this->Annex );
    }

        /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testSaveFilenameOK(){
        $newAnnexe['Annex']['filename']='Arreté_du_13_novembre_2013_version.pdf';
        //$newAnnexe['Annex']['filename']='1.pdf';
        $newAnnexe['Annex']['foreign_key']='1';
        $newAnnexe['Annex']['size']='150';
        
        $annexe=$this->Annex->save($newAnnexe);
        
        $this->assertEquals( 1, $annexe['Annex']['id'], var_export( $this->Annex->validationErrors, true));
    }
}

?>