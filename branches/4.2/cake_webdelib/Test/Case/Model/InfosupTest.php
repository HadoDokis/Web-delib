<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CakePHP InfosupTest
 * @author Florian Ajir <florian.ajir@adullact.org>
 */

/**
 * La classe InfosupTest
 *
 * @package app.Test.Case.Model
 */
class InfosupTest extends CakeTestCase {

    /**
     *
     * @var Model
     */
    public $Infosup = null;

    /**
     * Fixtures utilisés.
     *
     * @var array
     */
    public $fixtures = array(
        'app.Infosup',
        'app.Infosupdef',
        'app.Infosuplistedef',
    );

    /**
     * Préparation du test.
     */
    public function setUp() {
        parent::setUp();
        $this->Infosup = ClassRegistry::init('Infosup');
    }

    /**
     * Nettoyage postérieur au test.
     */
    public function tearDown() {
        unset($this->InfosupTest);
        parent::tearDown();
    }

    public function testGedoooReadAll() {
        $result = $this->Infosup->gedoooReadAll('Deliberation', 52);
        $expected = array(
            array(
                'Infosup' => array(
                    'id' => 11,
                    'model' => 'Deliberation',
                    'foreign_key' => 52,
                    'infosupdef_id' => 5,
                    'text' => 'Patricia Rival',
                    'date' => null,
                    'file_name' => null,
                    'file_size' => null,
                    'file_type' => null,
                    'content' => null
                ),
                'Infosupdef' => array(
                    'id' => 5,
                    'model' => 'Deliberation',
                    'nom' => 'text protege',
                    'commentaire' => '',
                    'ordre' => 4,
                    'code' => 'textprotected',
                    'type' => 'text',
                    'val_initiale' => 'valeur initiale',
                    'recherche' => false,
                    'created' => '2013-06-11 14:48:32',
                    'modified' => '2013-06-21 14:49:40',
                    'actif' => true
                ),
                'Infosuplistedef' => array(
                    'id' => null,
                    'infosupdef_id' => null,
                    'ordre' => null,
                    'nom' => null,
                    'actif' => null,
                    'created' => null,
                    'modified' => null
                )
            ),
            array(
                'Infosup' => array(
                    'id' => 12,
                    'model' => 'Deliberation',
                    'foreign_key' => 52,
                    'infosupdef_id' => 6,
                    'text' => '1',
                    'date' => null,
                    'file_name' => null,
                    'file_size' => null,
                    'file_type' => null,
                    'content' => null
                ),
                'Infosupdef' => array(
                    'id' => 6,
                    'model' => 'Deliberation',
                    'nom' => 'liste protected',
                    'commentaire' => '',
                    'ordre' => 5,
                    'code' => 'listenoneditable',
                    'type' => 'list',
                    'val_initiale' => '',
                    'recherche' => false,
                    'created' => '2013-06-11 14:48:57',
                    'modified' => '2013-06-21 15:41:23',
                    'actif' => true
                ),
                'Infosuplistedef' => array(
                    'id' => 1,
                    'infosupdef_id' => 6,
                    'ordre' => 1,
                    'nom' => 'Aiguerelles',
                    'actif' => true,
                    'created' => '2013-06-11 14:58:08',
                    'modified' => '2013-06-11 14:58:08'
                )
            )
        );

        $this->assertEquals($result, $expected, var_export($result, true));

        $resultEmpty = $this->Infosup->gedoooReadAll('Deliberation', 1);
        $this->assertEquals($resultEmpty, array(), var_export($resultEmpty, true));
    }

}
