<?php

/**
 * Code source de la classe ActionFixture.
 *
 * PHP 5.3
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * Classe ActionFixture.
 *
 * @package app.Test.Fixture
 */
class CircuitUserFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    var $import = array('table' => 'users_circuits', 'records' => false);
    public $records;

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public function init() {
        $this->records = array(
            array(
                'id' => '1',
                'user_id' => '1',
                'circuit_id' => '1',
            ),
            array(
                'id' => '2',
                'user_id' => '2',
                'circuit_id' => '2',
            ),
            array(
                'id' => '3',
                'user_id' => '3',
                'circuit_id' => '3',
            ),
            array(
                'id' => '4',
                'user_id' => '4',
                'circuit_id' => '3',
            ),
            array(
                'id' => '5',
                'user_id' => '5',
                'circuit_id' => '2',
            ),
            array(
                'id' => '6',
                'user_id' => '6',
                'circuit_id' => '2',
            ),
            array(
                'id' => '7',
                'user_id' => '7',
                'circuit_id' => '4',
            ),
        );

        parent::init();
    }

}
