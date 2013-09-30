<?php

	class UserCircuitFixture extends CakeTestFixture {
		var $name = 'UserCircuit';
		var $table = 'users_circuits';
		var $import = array( 'table' => 'users_circuits', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'user_id' => '2',
				'circuit_id' => '1',
				'service_id' => '2',
				'position' => '1',
			),
			array(
				'id' => '2',
				'user_id' => '3',
				'circuit_id' => '1',
				'service_id' => '2',
				'position' => '2',
			),
			array(
				'id' => '3',
				'user_id' => '4',
				'circuit_id' => '1',
				'service_id' => '2',
				'position' => '3',
			),
			array(
				'id' => '4',
				'user_id' => '2',
				'circuit_id' => '2',
				'service_id' => '2',
				'position' => '1',
			),
		);
	}

?>