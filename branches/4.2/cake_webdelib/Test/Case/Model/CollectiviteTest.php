<?php

/**
 * Code source de la classe CollectiviteTest.
 *
 * PHP 5.3
 *
 * @package app.Test.Case.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('Collectivite', 'Model');

/**
 * La classe CollectiviteTest ...
 *
 * @package app.Test.Case.Model
 */
class CollectiviteTest extends CakeTestCase {

    /**
     *
     * @var Model
     */
    public $Collectivite = null;

    /**
     * Fixtures utilisés.
     *
     * @var array
     */
    public $fixtures = array(
        'app.Collectivite'
    );

    /**
     * Préparation du test.
     */
    public function setUp() {
        parent::setUp();
        $this->Collectivite = ClassRegistry::init('Collectivite');
    }

    /**
     * Nettoyage postérieur au test.
     */
    public function tearDown() {
        unset($this->Collectivite);
        parent::tearDown();
    }

    /**
     * Test de la méthode Collectivite::gedoooRead()
     */
    public function testGedoooRead() {
        $result = $this->Collectivite->gedoooRead(1);
        $expected = array(
            'Collectivite' => array(
                'id' => 1,
                'id_entity' => 1,
                'nom' => 'ADULLACT',
                'adresse' => '836, rue du Mas de Verchant',
                'CP' => 34000,
                'ville' => 'Montpellier',
                'telephone' => '04 67 65 05 88'
            )
        );
        $this->assertEquals($result, $expected, var_export($result, true));
    }

    /**
     * Test de la méthode Collectivite::gedoooNormalize()
     */
    public function testGedoooNormalize(){
        $record = $this->Collectivite->gedoooRead(1);
        $result = $this->Collectivite->gedoooNormalize($record);
        
        $date_jour_courant = DateFrench::frenchDate(strtotime("now"));
        $date_du_jour = date("d/m/Y", strtotime("now"));
        
        $expected = array(
            'Collectivite' => array(
                'id' => 1,
                'id_entity' => 1,
                'nom' => 'ADULLACT',
                'adresse' => '836, rue du Mas de Verchant',
                'CP' => 34000,
                'ville' => 'Montpellier',
                'telephone' => '04 67 65 05 88',
                'date_jour_courant' => $date_jour_courant,
                'date_du_jour' => $date_du_jour
            )
        );
        
        $this->assertEquals($result, $expected, var_export($result, true));
        
        $resultEmpty = $this->Collectivite->gedoooNormalize(array());
        
        $this->assertEquals($resultEmpty, array(), var_export($resultEmpty, true));
    }
    
}

?>
