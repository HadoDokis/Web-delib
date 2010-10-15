<?php

	class UserServiceFixture extends CakeTestFixture {
		var $name = 'UserService';
		var $table = 'users_services';
		var $import = array( 'table' => 'users_services', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'user_id' => '1',
				'service_id' => '1',
			),
			array(
				'id' => '2',
				'user_id' => '2',
				'service_id' => '2',
			),
			array(
				'id' => '3',
				'user_id' => '3',
				'service_id' => '2',
			),
			array(
				'id' => '4',
				'user_id' => '4',
				'service_id' => '2',
			),
			array(
				'id' => '5',
				'user_id' => '5',
				'service_id' => '2',
			),
			array(
				'id' => '6',
				'user_id' => '6',
				'service_id' => '2',
			),
			array(
				'id' => '7',
				'user_id' => '7',
				'service_id' => '2',
			),
		);
	}

?>