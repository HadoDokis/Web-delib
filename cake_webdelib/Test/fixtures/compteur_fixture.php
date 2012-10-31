<?php

	class CompteurFixture extends CakeTestFixture {
		var $name = 'Compteur';
		var $table = 'compteurs';
		var $import = array( 'table' => 'compteurs', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'nom' => 'Compteur',
				'commentaire' => null,
				'def_compteur' => '#s#',
				'sequence_id' => '1',
				'def_reinit' => '#AAAA#',
				'val_reinit' => null,
				'created' => '2010-04-27 12:13:04',
				'modified' => '2010-04-27 12:13:04',
			),
		);
	}

?>