<?php

	class TypeseanceTypeacteurFixture extends CakeTestFixture {
		var $name = 'TypeseanceTypeacteur';
		var $table = 'typeseances_typeacteurs';
		var $import = array( 'table' => 'typeseances_typeacteurs', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'typeseance_id' => '1',
				'typeacteur_id' => '1',
			),
		);
	}

?>