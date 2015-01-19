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
class UserFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    var $import = array('model' => 'User', 'records' => false);
    
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
                'login' => 'login1',
                'nom' => 'nom1',
                'prenom' => 'prenom1',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => '2',
                'login' => 'login2',
                'nom' => 'nom2',
                'prenom' => 'prenom2',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => '3',
                'login' => 'login3',
                'nom' => 'nom3',
                'prenom' => 'prenom3',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => '4',
                'login' => 'login4',
                'nom' => 'nom4',
                'prenom' => 'prenom4',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => '5',
                'login' => 'login5',
                'nom' => 'nom5',
                'prenom' => 'prenom5',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => '6',
                'login' => 'login6',
                'nom' => 'nom6',
                'prenom' => 'prenom6',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => '7',
                'login' => 'login7',
                'nom' => 'nom7',
                'prenom' => 'prenom7',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ),
        );

        parent::init();
    }

}
