<?php

/**
 * Code source de la classe DeliberationseanceFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe DeliberationseanceFixture ...
 *
 * @package app.Test.Fixture
 */
class DeliberationseanceFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Deliberationseance',
        'records' => false
    );

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public $records = array(
        array(
            'id' => 285,
            'deliberation_id' => 217,
            'seance_id' => 31,
            'position' => 10,
            'avis' => NULL,
            'commentaire' => NULL,
        ),
        array(
            'id' => 372,
            'deliberation_id' => 265,
            'seance_id' => 45,
            'position' => 1,
            'avis' => NULL,
            'commentaire' => NULL,
        ),
        array(
            'id' => 330,
            'deliberation_id' => 169,
            'seance_id' => 20,
            'position' => 52,
            'avis' => NULL,
            'commentaire' => NULL,
        ),
        array(
            'id' => 450,
            'deliberation_id' => 276,
            'seance_id' => 20,
            'position' => 1,
            'avis' => NULL,
            'commentaire' => NULL,
        ),
        array(
            'id' => 517,
            'deliberation_id' => 276,
            'seance_id' => 31,
            'position' => 3,
            'avis' => NULL,
            'commentaire' => NULL,
        ),
    );

}

?>
