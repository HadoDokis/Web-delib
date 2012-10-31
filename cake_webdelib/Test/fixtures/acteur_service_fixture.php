<?php

	class ActeurServiceFixture extends CakeTestFixture {
		var $name = 'ActeurService';
		var $table = 'acteurs_services';
		var $import = array( 'table' => 'acteurs_services', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'acteur_id' => '1',
				'service_id' => '1',
			),
			array(
				'id' => '2',
				'acteur_id' => '2',
				'service_id' => '1',
			),
		);
	}

?>