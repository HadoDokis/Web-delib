<?php

	class ThemeFixture extends CakeTestFixture {
		var $name = 'Theme';
		var $table = 'themes';
		var $import = array( 'table' => 'themes', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'parent_id' => '0',
				'order' => null,
				'libelle' => 'Défaut',
				'actif' => '1',
				'created' => '2010-04-27 12:12:47',
				'modified' => '2010-04-27 12:12:47',
			),
			array(
				'id' => '2',
				'parent_id' => '1',
				'order' => null,
				'libelle' => 'Sous - Défaut',
				'actif' => '1',
				'created' => '2010-04-27 12:12:52',
				'modified' => '2010-04-27 12:12:52',
			),
		);
	}

?>