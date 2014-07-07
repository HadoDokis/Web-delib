<?php

	class TypeacteurFixture extends CakeTestFixture {
		var $name = 'Typeacteur';
		var $table = 'typeacteurs';
		var $import = array( 'table' => 'typeacteurs', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'nom' => 'Elu(s) majorité',
				'commentaire' => null,
				'elu' => '1',
				'created' => '2010-04-27 12:12:27',
				'modified' => '2010-04-27 12:12:27',
			),
		);
	}

?>