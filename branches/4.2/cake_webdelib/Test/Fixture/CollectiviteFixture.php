Error reading included file Templates/CakePHP/Test/freemarker_functions.ftl<?php
	/**
	 * Code source de la classe CollectiviteFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CollectiviteFixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class CollectiviteFixture extends CakeTestFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Collectivite',
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
                'id_entity' => 1,
                'nom' => 'ADULLACT',
                'adresse' => '836, rue du Mas de Verchant',
                'CP' => 34000,
                'ville' => 'Montpellier',
                'telephone' => '04 67 65 05 88'
            )
		);

	}
?>