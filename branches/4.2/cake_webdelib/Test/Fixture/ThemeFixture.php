<?php
	/**
	 * Code source de la classe ThemeFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ThemeFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class ThemeFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Theme',
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
                'parent_id' => null,
                'order' => 1,
                'libelle' => 'Thème 1',
                'actif' => true,
                'created' => '2013-08-26 22:12:10',
                'modified' => '2013-08-26 22:12:10',
                'lft' => 1,
                'rght' => 6
            ),
            array(
                'id' => 2,
                'parent_id' => 1,
                'order' => 2,
                'libelle' => 'Thème 1.1',
                'actif' => true,
                'created' => '2013-08-26 22:12:10',
                'modified' => '2013-08-26 22:12:10',
                'lft' => 2,
                'rght' => 3
            ),
            array(
                'id' => 3,
                'parent_id' => 1,
                'order' => 3,
                'libelle' => 'Thème 1.2',
                'actif' => true,
                'created' => '2013-08-26 22:12:10',
                'modified' => '2013-08-26 22:12:10',
                'lft' => 4,
                'rght' => 5
            ),
		);
	}
?>