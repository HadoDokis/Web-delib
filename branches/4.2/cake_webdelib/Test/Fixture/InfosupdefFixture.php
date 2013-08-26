<?php

/**
 * Code source de la classe InfosupdefFixture.
 *
 * @package app.Test.Fixture
 */

/**
 * La classe InfosupdefFixture
 *
 * @package app.Test.Fixture
 */
class InfosupdefFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Infosupdef',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
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
        array(
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
        )
    );

}

?>
