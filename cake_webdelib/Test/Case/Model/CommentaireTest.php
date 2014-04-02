<?php

/**
 * CakePHP CommentaireTest
 * @author Florian Ajir <florian.ajir@adullact.org>
 */

/**
 * La classe CommentaireTest
 *
 * @package app.Test.Case.Model
 */
class CommentaireTest extends CakeTestCase {

    /**
     *
     * @var Model
     */
    public $Commentaire = null;

    /**
     * Fixtures utilisés.
     *
     * @var array
     */
    public $fixtures = array(
        'app.Commentaire'
    );

    /**
     * Préparation du test.
     */
    public function setUp() {
        parent::setUp();
        $this->Commentaire = ClassRegistry::init('Commentaire');
    }

    /**
     * Nettoyage postérieur au test.
     */
    public function tearDown() {
        unset($this->Commentaire);
        parent::tearDown();
    }

    /**
     * test de la méthode Commentaire::gedoooReadAll
     */
    public function testGedoooReadAll() {
        $result = $this->Commentaire->gedoooReadAll(1);
        $expected = array(
            array(
                'Commentaire' => array(
                    'texte' => 'A revoir (cf notre conversation du 5/06)',
                    'commentaire_auto' => false,
                )
            ),
            array(
                'Commentaire' => array(
                    'texte' => 'OK, pris en compte',
                    'commentaire_auto' => true,
                )
        ));
        $this->assertEquals($result, $expected, var_export($result, true));


        $resultEmpty = $this->Commentaire->gedoooReadAll(10);
        $this->assertEquals($resultEmpty, array(), var_export($resultEmpty, true));
    }

    /**
     * test de la méthode Commentaire::gedoooNormalizeAll
     */
    public function testGedoooNormalizeAll() {
        $datas = $this->Commentaire->gedoooReadAll(1);
        $result = $this->Commentaire->gedoooNormalizeAll(array('Commentaires' => $datas));
        $expected = array(
            'AvisCommission' => array(
                array(
                    'avis' => 'OK, pris en compte'
                )
            ),
            'Commentaires' => array(
                array(
                    'avis' => 'A revoir (cf notre conversation du 5/06)'
                )
            )
        );
        $this->assertEquals($result, $expected, var_export($result, true));
    }

}
