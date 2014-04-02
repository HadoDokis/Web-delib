<?php

/**
 * Code source de la classe InfosupFixture.
 *
 * @package app.Test.Fixture
 */

/**
 * La classe InfosupFixture
 *
 * @package app.Test.Fixture
 */
class InfosupFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Infosup',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
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
        array(
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
        array(
            'id' => 1,
            'model' => 'Seance',
            'foreign_key' => 1,
            'infosupdef_id' => 7,
            'text' => '1',
            'date' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'content' => null
        ),
        array(
            'id' => 2,
            'model' => 'Seance',
            'foreign_key' => 1,
            'infosupdef_id' => 8,
            'text' => null,
            'date' => '2013-08-26',
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'content' => null
        ),
        array(
            'id' => 3,
            'model' => 'Seance',
            'foreign_key' => 1,
            'infosupdef_id' => 9,
            'text' => 'nonactif',
            'date' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'content' => null
        ),
        array(
            'id' => 4,
            'model' => 'Seance',
            'foreign_key' => 1,
            'infosupdef_id' => 10,
            'text' => '3',
            'date' => null,
            'file_name' => null,
            'file_size' => null,
            'file_type' => null,
            'content' => null
        ),
    );

}

?>
