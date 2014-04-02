<?php

	class CollectiviteFixture extends CakeTestFixture {
		var $name = 'Collectivite';
		var $table = 'collectivites';
		var $import = array( 'table' => 'collectivites', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'nom' => 'Adullact',
				'adresse' => '335, Cour Messier',
				'CP' => '34000',
				'ville' => 'Montpellier',
				'telephone' => '0467650588',
			),
		);
	}

?>