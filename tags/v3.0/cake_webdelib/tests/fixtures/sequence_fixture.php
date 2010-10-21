<?php

	class SequenceFixture extends CakeTestFixture {
		var $name = 'Sequence';
		var $table = 'sequences';
		var $import = array( 'table' => 'sequences', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'nom' => 'Sequence',
				'commentaire' => null,
				'num_sequence' => '0',
				'created' => '2010-04-27 12:12:56',
				'modified' => '2010-04-27 12:12:56',
			),
		);
	}

?>