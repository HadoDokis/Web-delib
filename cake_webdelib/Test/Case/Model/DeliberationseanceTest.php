<?php

/**
 * Code source de la classe DeliberationseanceTest.
 *
 * @package app.Test.Case.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('Deliberationseance', 'Model');

/**
 * La classe DeliberationseanceTest ...
 *
 * @package app.Test.Case.Model
 */
class DeliberationseanceTest extends CakeTestCase {

    /**
     * Fixtures utilisés.
     *
     * @var array
     */
    public $fixtures = array(
        'app.Deliberationseance',
        'app.Seance',
        'app.Acteur',
        'app.Typeseance',
        'app.Infosup',
        'app.Infosupdef',
        'app.Infosuplistedef',
    );

    /**
     * Préparation du test.
     */
    public function setUp() {
        parent::setUp();
        $this->Deliberationseance = ClassRegistry::init('Deliberationseance');
    }

    /**
     * Nettoyage postérieur au test.
     */
    public function tearDown() {
        unset($this->Deliberationseance);
        parent::tearDown();
    }

    /**
     * Test de la méthode Deliberationseance::method()
     */
    public function testGedoooRead() {
        // pour charger la fonction alias_querydata
        include_once('../../plugins/Database/Config/bootstrap.php');
        $result = $this->Deliberationseance->gedoooRead(276);
//        debug($result);
        
        $this->markTestIncomplete('Ce test n\'a pas encoré été terminé (manque assert).');
    }

}

?>
