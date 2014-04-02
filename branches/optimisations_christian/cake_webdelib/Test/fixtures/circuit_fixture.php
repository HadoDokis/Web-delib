<?php

	class CircuitFixture extends CakeTestFixture {
		var $name = 'Circuit';
		var $table = 'circuits';
		var $import = array( 'table' => 'circuits', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'libelle' => 'Circuit de test',
			),
			array(
				'id' => '2',
				'libelle' => 'test_circuit_paraf',
			),
		);
	}

?>