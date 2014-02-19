<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
App::uses('Signature', 'Lib');
class SignatureTest extends CakeTestCase {

    private $Signature;

    public function setUp() {
        parent::setUp();
        $this->Signature = new Signature;
    }

    /**
     * Méthode exécutée avant chaque test.
     *
     * @return void
     */
    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Test listCircuits()
     * @return void
     */
    public function testListCircuits(){
        Configure::write('PARAPHEUR', 'IPARAPHEUR');
        return ($this->Signature->listCircuits());
        Configure::write('PARAPHEUR', 'PASTELL');
        return ($this->Signature->listCircuits());
    }

    /**
     * Test updateAll()
     * @return void
     */
    public function testUpdateAll(){
        $retour = $this->Signature->updateAll();
        debug ($retour);
        if (Configure::read('PARAPHEUR') == 'IPARAPHEUR'){
            assert($retour == 'TRAITEMENT_TERMINE_OK');
        }
    }


}
