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

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'Deliberation',
        'Collectivite',
        'Typeacte'
    );

    public function setUp() {
        parent::setUp();
        $this->Deliberation = ClassRegistry::init('Deliberation');
        $this->Collectivite = ClassRegistry::init('Collectivite');
        $this->Signature = new Signature();
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
    public function testListCircuits() {
        Configure::write('PARAPHEUR', 'IPARAPHEUR');
        $aIparapheur = $this->Signature->listCircuits();

        Configure::write('PARAPHEUR', 'PASTELL');
        $aPastell = $this->Signature->listCircuits();

        $this->assertEquals($aIparapheur, $aPastell, var_export(array_combine($aIparapheur, $aPastell), true));
    }

    /**
     * Test updateAll()
     * @return void
     */
    public function testUpdateAll() {
        $retour = $this->Signature->updateAll();
        if (Configure::read('PARAPHEUR') == 'IPARAPHEUR') {
            assert($retour == 'TRAITEMENT_TERMINE_OK');
        }
    }

    /**
     * Test send()
     * @return void
     */
    public function testsend() {
        $this->Deliberation->id = 1;
        $this->Deliberation->Behaviors->load('Containable');
        $target = $this->Deliberation->find('first', array(
            'contain' => array('Typeacte.nature_id'),
            'conditions' => array('Deliberation.id' => $this->Deliberation->id)
        ));
        $libelleSousType = 'Délibération';

        $aDelegToParapheurDocuments = array(
            'docPrincipale' => file_get_contents(APP . 'Test/Data/AnnexFixture.pdf'),
            'annexes' => array(file_get_contents(APP . 'Test/Data/AnnexFixture.pdf')));

        $ret = $this->Signature->send($target, $libelleSousType, $aDelegToParapheurDocuments['docPrincipale'], $aDelegToParapheurDocuments['annexes']);

        //FIX suppression dans pastell du fichier
    }

}
