<?php

/**
 * Code source de la classe TypeacteurFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe TypeacteurFixture ...
 *
 * @package app.Test.Fixture
 */
class TypeacteurFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Typeacteur',
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
            'nom' => 'Majorité',
            'commentaire' => 'la parti élu',
            'elu' => true,
            'created' => '2013-02-14 16:09:51',
            'modified' => '2013-02-14 16:09:51',
        ),
        array(
            'id' => 2,
            'nom' => 'Opposition',
            'commentaire' => 'le parti d\'opposition',
            'elu' => true,
            'created' => '2013-02-14 16:10:00',
            'modified' => '2013-02-14 16:10:00',
        ),
        array(
            'id' => 3,
            'nom' => 'Autres',
            'commentaire' => 'les autres acteurs',
            'elu' => false,
            'created' => '2013-02-14 16:10:15',
            'modified' => '2013-02-14 16:10:15',
        ),
    );

}

?>
