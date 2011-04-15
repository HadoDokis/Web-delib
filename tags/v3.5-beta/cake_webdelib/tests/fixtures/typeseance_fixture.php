<?php

	class TypeseanceFixture extends CakeTestFixture {
		var $name = 'Typeseance';
		var $table = 'typeseances';
		var $import = array( 'table' => 'typeseances', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'libelle' => 'Type de Test',
				'retard' => '0',
				'action' => '0',
				'compteur_id' => '1',
				'modelprojet_id' => '1',
				'modeldeliberation_id' => '1',
				'modelconvocation_id' => '1',
				'modelordredujour_id' => '1',
				'modelpvsommaire_id' => '1',
				'modelpvdetaille_id' => '1',
				'created' => '2010-04-27 12:13:17',
				'modified' => '2010-05-04 15:11:31',
			),
		);
	}

?>