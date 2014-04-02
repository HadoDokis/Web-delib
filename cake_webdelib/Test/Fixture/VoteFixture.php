<?php

/**
 * Code source de la classe VoteFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe VoteFixture ...
 *
 * @package app.Test.Fixture
 */
class VoteFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Vote',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
//        array(
//            'id' => 790,
//            'acteur_id' => 1,
//            'delib_id' => 276,
//            'resultat' => 3,
//            'created' => '2013-08-19 17:01:43',
//            'modified' => '2013-08-19 17:01:43',
//        ),
        array(
            'id' => 791,
            'acteur_id' => 2,
            'delib_id' => 276,
            'resultat' => 3,
            'created' => '2013-08-19 17:01:43',
            'modified' => '2013-08-19 17:01:43',
        ),
        array(
            'id' => 792,
            'acteur_id' => 3,
            'delib_id' => 276,
            'resultat' => 3,
            'created' => '2013-08-19 17:01:43',
            'modified' => '2013-08-19 17:01:43',
        ),
        array(
            'id' => 4,
            'acteur_id' => 4,
            'delib_id' => 276,
            'resultat' => 3,
            'created' => '2013-08-19 17:01:43',
            'modified' => '2013-08-19 17:01:43',
        ),
        array(
            'id' => 5,
            'acteur_id' => 5,
            'delib_id' => 276,
            'resultat' => 3,
            'created' => '2013-08-19 17:01:43',
            'modified' => '2013-08-19 17:01:43',
        ),
    );

}

?>
