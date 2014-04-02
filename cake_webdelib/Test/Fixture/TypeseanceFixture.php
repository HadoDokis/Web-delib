<?php

/**
 * Code source de la classe TypeseanceFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe TypeseanceFixture ...
 *
 * @package app.Test.Fixture
 */
class TypeseanceFixture extends CakeTestFixture {

    /**
     * On importe la définition de la table, pas les enregistrements.
     *
     * @var array
     */
    public $import = array(
        'model' => 'Typeseance',
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
            'libelle' => '1ère Commission',
            'retard' => 1,
            'action' => 1,
            'compteur_id' => 1,
            'modelprojet_id' => 1,
            'modeldeliberation_id' => 1,
            'modelconvocation_id' => 1,
            'modelordredujour_id' => 1,
            'modelpvsommaire_id' => 1,
            'modelpvdetaille_id' => 1,
            'created' => '2013-02-15 15:26:09',
            'modified' => '2013-04-22 12:09:10',
        ),
        array(
            'id' => 2,
            'libelle' => '2ème Commission',
            'retard' => 2,
            'action' => 1,
            'compteur_id' => 2,
            'modelprojet_id' => 2,
            'modeldeliberation_id' => 276,
            'modelconvocation_id' => 2,
            'modelordredujour_id' => 2,
            'modelpvsommaire_id' => 2,
            'modelpvdetaille_id' => 2,
            'created' => '2013-02-15 15:26:09',
            'modified' => '2013-04-22 12:09:10',
        ),
    );

}

?>
