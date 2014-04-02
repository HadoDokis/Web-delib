<?php
/**
 * Code source de la classe SeanceFixture.
 *
 * @package app.Test.Fixture
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe SeanceFixture ...
 *
 * @package app.Test.Fixture
 */
class SeanceFixture extends CakeTestFixture
{
	/**
	 * On importe la définition de la table, pas les enregistrements.
	 *
	 * @var array
	 */
	public $import = array(
		'model' => 'Seance',
		'records' => false
	);

	/**
	 * Définition des enregistrements.
	 *
	 * @var array
	 */
	public $records = array(
            array (
                'id' => 20,
                'type_id' => 1,
                'created' => '2013-04-05 11:42:55',
                'modified' => '2013-08-19 17:26:21',
                'date_convocation' => '2013-08-19 17:26:21',
                'date' => '2013-06-27 19:00:00',
                'traitee' => 0,
                'commentaire' => NULL,
                'secretaire_id' => 2,
                'president_id' => 1,
                'debat_global' => NULL,
                'debat_global_name' => NULL,
                'debat_global_size' => NULL,
                'debat_global_type' => NULL,
                'pv_figes' => NULL,
                'pv_sommaire' => NULL,
                'pv_complet' => NULL,
              ),
	);

}
?>
