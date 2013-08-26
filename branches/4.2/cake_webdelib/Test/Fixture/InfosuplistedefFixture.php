<?php

/**
 * Code source de la classe InfosuplistedefFixture.
 *
 * @package app.Test.Fixture
 */

/**
 * La classe InfosuplistedefFixture
 *
 * @package app.Test.Fixture
 */
class InfosuplistedefFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Infosuplistedef',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
            'id' => 1,
            'infosupdef_id' => 6,
            'ordre' => 1,
            'nom' => 'Aiguerelles',
            'actif' => true,
            'created' => '2013-06-11 14:58:08',
            'modified' => '2013-06-11 14:58:08'
        ),
        array(
            'id' => 3,
            'infosupdef_id' => 6,
            'ordre' => 2,
            'nom' => 'Cévennes',
            'actif' => true,
            'created' => '2013-06-11 14:58:21',
            'modified' => '2013-06-11 14:58:23'
        ),
        array(
            'id' => 2,
            'infosupdef_id' => 6,
            'ordre' => 3,
            'nom' => 'Mosson',
            'actif' => true,
            'created' => '2013-06-11 14:58:14',
            'modified' => '2013-06-11 14:58:23'
        )
    );

}

?>
