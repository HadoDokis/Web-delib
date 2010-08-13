<?php

	class ActeurFixture extends CakeTestFixture {
		var $name = 'Acteur';
		var $table = 'acteurs';
		var $import = array( 'table' => 'acteurs', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'typeacteur_id' => '1',
				'nom' => 'Test',
				'prenom' => 'Test',
				'salutation' => 'Monsieur',
				'titre' => null,
				'position' => '1',
				'date_naissance' => null,
				'adresse1' => null,
				'adresse2' => null,
				'cp' => null,
				'ville' => null,
				'email' => null,
				'telfixe' => null,
				'telmobile' => null,
				'note' => null,
				'created' => '2010-04-27 12:12:35',
				'modified' => '2010-04-27 12:12:35',
			),
			array(
				'id' => '2',
				'typeacteur_id' => '1',
				'nom' => 'Selenium',
				'prenom' => 'Selenium',
				'salutation' => 'Madame',
				'titre' => null,
				'position' => '2',
				'date_naissance' => null,
				'adresse1' => null,
				'adresse2' => null,
				'cp' => null,
				'ville' => null,
				'email' => null,
				'telfixe' => null,
				'telmobile' => null,
				'note' => null,
				'created' => '2010-04-27 12:12:41',
				'modified' => '2010-04-27 12:12:41',
			),
		);
	}

?>
