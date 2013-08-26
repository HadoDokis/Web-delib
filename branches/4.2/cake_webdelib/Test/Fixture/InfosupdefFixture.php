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
        ),
        array(
            'id' => 7,
            'model' => 'Seance',
            'nom' => 'booleen',
            'commentaire' => '',
            'ordre' => 6,
            'code' => 'booleantest',
            'type' => 'boolean',
            'val_initiale' => '',
            'recherche' => false,
            'created' => '2013-06-11 14:48:57',
            'modified' => '2013-06-21 15:41:23',
            'actif' => true
        ),
        array(
            'id' => 8,
            'model' => 'Seance',
            'nom' => 'datation',
            'commentaire' => '',
            'ordre' => 8,
            'code' => 'datetest',
            'type' => 'date',
            'val_initiale' => '',
            'recherche' => false,
            'created' => '2013-06-11 14:48:57',
            'modified' => '2013-06-21 15:41:23',
            'actif' => true
        ),
        array(
            'id' => 9,
            'model' => 'Seance',
            'nom' => 'desactive',
            'commentaire' => '',
            'ordre' => 9,
            'code' => 'nonactive',
            'type' => 'text',
            'val_initiale' => '',
            'recherche' => false,
            'created' => '2013-06-11 14:48:57',
            'modified' => '2013-06-21 15:41:23',
            'actif' => false
        ),
        array(
            'id' => 10,
            'model' => 'Seance',
            'nom' => 'liste seance',
            'commentaire' => '',
            'ordre' => 10,
            'code' => 'listeseance',
            'type' => 'list',
            'val_initiale' => '',
            'recherche' => false,
            'created' => '2013-06-11 14:48:57',
            'modified' => '2013-06-21 15:41:23',
            'actif' => true
        ),
        
    );

}

?>
