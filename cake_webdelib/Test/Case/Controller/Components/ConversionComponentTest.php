<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses('ComponentCollection', 'Controller');
App::uses('ConversionComponent', 'Controller/Component' );
App::uses('File', 'Utility');

/**
* Classe Histochoixcer93Test.
*
* @package app.Test.Case.Model
* 
*/

class ConversionComponentTest extends CakeTestCase {
    public $ConversionComponent = null;
    public $Controller = null;
    

    public function setUp() {
        parent::setUp();
        $Collection = new ComponentCollection();
        $this->ConversionComponent = new ConversionComponent($Collection);
    }
    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    */
    public function tearDown() {
        parent::tearDown();
        unset( $this->ConversionComponent );
        unset($this->Controller);
    }

        /**
    * Méthode exécutée après chaque test.
    *
    * @return void
    */
    public function testconvertirFlux(){
        
        $file=new File(WWW_ROOT.DS.'check'.DS.'files'.DS.'checkConversion.odt');
        $file->close();
        $result=$this->ConversionComponent->convertirFlux($file->read(),'odt', 'pdf');
        $file=new File(TMP.DS.'files'.DS.'checkConversion.pdf',TRUE);
        $file->append($result);
        $expected = $file->mime();
        $file->close(); 
        
        $this->assertEquals($expected, 'application/pdf');
    }


}

?>