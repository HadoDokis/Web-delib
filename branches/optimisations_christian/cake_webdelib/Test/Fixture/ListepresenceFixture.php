<?php

/**
 * Code source de la classe ListepresenceFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe ListepresenceFixture ...
 *
 * @package app.Test.Fixture
 */
class ListepresenceFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Listepresence',
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
            'delib_id' => 1,
            'acteur_id' => 1,
            'present' => false,
            'mandataire' => 1,
            'suppleant_id' => NULL,
        ),
        array(
            'id' => 2,
            'delib_id' => 1,
            'acteur_id' => 2,
            'present' => true,
            'mandataire' => 2,
            'suppleant_id' => NULL,
        ),
        array(
            'id' => 3,
            'delib_id' => 134,
            'acteur_id' => 3,
            'present' => true,
            'mandataire' => 3,
            'suppleant_id' => NULL,
        ), 
        array(
            'id' => 4,
            'delib_id' => 276,
            'acteur_id' => 4,
            'present' => true,
            'mandataire' => 4,
            'suppleant_id' => 5,
        )
    );

}

?>
