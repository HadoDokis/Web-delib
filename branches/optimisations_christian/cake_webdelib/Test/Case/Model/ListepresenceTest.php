<?php

/**
 * Code source de la classe ListepresenceTest.
 *
 * @package app.Test.Case.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('Listepresence', 'Model');

/**
 * La classe ListepresenceTest ...
 *
 * @package app.Test.Case.Model
 */
class ListepresenceTest extends CakeTestCase {

    /**
     *
     * @var Model
     */
    public $Listepresence = null;

    /**
     * Fixtures utilisés.
     *
     * @var array
     */
    public $fixtures = array(
        'app.Listepresence',
        'app.Acteur',
        'app.Typeacteur',
        'app.Vote',
    );

    /**
     * Préparation du test.
     */
    public function setUp() {
        parent::setUp();
        $this->Listepresence = ClassRegistry::init('Listepresence');
    }

    /**
     * Nettoyage postérieur au test.
     */
    public function tearDown() {
        unset($this->Listepresence);
        parent::tearDown();
    }

    /**
     * Test de la méthode Listepresence::gedoooReadAll()
     */
    public function testGedoooReadAll() {
        // pour charger la fonction alias_querydata
        include_once('../../plugins/Database/Config/bootstrap.php');
        $result = $this->Listepresence->gedoooReadAll( 276 );
//        debug($result);



        $this->markTestIncomplete('Ce test n\'a pas encoré été terminé.');
    }
    /**
     * Test de la méthode Listepresence::gedoooNormalizeAll()
     */
    public function testGedoooNormalizeAll() {
//        $result = $this->Listepresence->gedoooNormalizeAll(  );
//        debug($result);

        $this->markTestIncomplete('Ce test n\'a pas encoré été terminé.');
    }

}

?>
