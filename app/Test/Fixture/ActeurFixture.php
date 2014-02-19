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

	class ActeurFixture extends CakeTestFixture {
		var $name = 'Acteur';
		var $table = 'acteurs';
		var $import = array( 'table' => 'acteurs', 'connection' => 'default', 'records' => true);
		var $records = array(
		);
	}

?>
