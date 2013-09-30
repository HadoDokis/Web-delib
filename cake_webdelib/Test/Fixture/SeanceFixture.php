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


class SeanceFixture extends CakeTestFixture {
        var $name = 'Seance';
        var $table = 'seances';
        var $import = array( 'table' => 'seances', 'connection' => 'default', 'records' => false);
        var $records = array(
                array(
                        'id' => '1',
                        'type_id' => '1',
                        'created' => '2010-05-25 15:44:05',
                        'modified' => '2010-05-25 15:44:05',
                        'date' => '2010-05-28 02:02:00',
                        'traitee' => '0',
                        'commentaire' => null,
                        'secretaire_id' => null,
                        'debat_global' => null,
                        'debat_global_name' => null,
                        'debat_global_size' => null,
                        'debat_global_type' => null,
                        'pv_figes' => null,
                        'pv_sommaire' => null,
                        'pv_complet' => null,
                ),
        );
}

?>