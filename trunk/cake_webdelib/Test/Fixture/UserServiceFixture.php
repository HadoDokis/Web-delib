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
class UserServiceFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    var $import = array('model' => 'UserService', 'records' => false);
    public $records;

    /**
     * Définition des enregistrements.
     *
     * @var array
     */
    public function init() {
        $this->records = array(
            array('id' => '1', 'user_id' => '1', 'service_id' => '1'),
            array('id' => '2', 'user_id' => '2', 'service_id' => '2'),
            array('id' => '3', 'user_id' => '3', 'service_id' => '3'),
            array('id' => '4', 'user_id' => '4', 'service_id' => '3'),
            array('id' => '5', 'user_id' => '5', 'service_id' => '4'),
            array('id' => '6', 'user_id' => '6', 'service_id' => '4'),
            array('id' => '7', 'user_id' => '6', 'service_id' => '3'),
            array('id' => '8', 'user_id' => '7', 'service_id' => '1'),
            array('id' => '9', 'user_id' => '7', 'service_id' => '4'),
        );

        parent::init();
    }

}
