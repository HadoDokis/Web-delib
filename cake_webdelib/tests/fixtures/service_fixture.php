<?php

	class ServiceFixture extends CakeTestFixture {
		var $name = 'Service';
		var $table = 'services';
		var $import = array( 'table' => 'services', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'parent_id' => '0',
				'order' => null,
				'libelle' => 'Défaut',
				'circuit_defaut_id' => '1',
				'actif' => '1',
				'created' => '2009-04-06 08:35:48',
				'modified' => '2010-05-03 17:06:58',
			),
			array(
				'id' => '2',
				'parent_id' => '0',
				'order' => null,
				'libelle' => 'TEST',
				'circuit_defaut_id' => '0',
				'actif' => '1',
				'created' => '2010-04-27 12:11:17',
				'modified' => '2010-04-27 12:11:17',
			),
			array(
				'id' => '3',
				'parent_id' => '2',
				'order' => null,
				'libelle' => 'Sous - TEST',
				'circuit_defaut_id' => '1',
				'actif' => '1',
				'created' => '2010-04-27 12:11:21',
				'modified' => '2010-05-03 17:07:26',
			),
		);
	}

?>