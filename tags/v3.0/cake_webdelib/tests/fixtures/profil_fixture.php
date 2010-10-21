<?php

	class ProfilFixture extends CakeTestFixture {
		var $name = 'Profil';
		var $table = 'profils';
		var $import = array( 'table' => 'profils', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'parent_id' => '0',
				'libelle' => 'Défaut',
				'actif' => '1',
				'created' => '2009-04-06 11:06:17',
				'modified' => '2009-04-06 11:06:17',
			),
			array(
				'id' => '2',
				'parent_id' => '0',
				'libelle' => 'Administrateur',
				'actif' => '1',
				'created' => '2009-04-06 11:06:39',
				'modified' => '2009-04-06 11:06:39',
			),
			array(
				'id' => '3',
				'parent_id' => '0',
				'libelle' => 'Rédacteur',
				'actif' => '1',
				'created' => '2009-04-06 11:06:48',
				'modified' => '2009-04-06 11:06:48',
			),
			array(
				'id' => '4',
				'parent_id' => '0',
				'libelle' => 'Valideur',
				'actif' => '1',
				'created' => '2009-04-06 11:06:54',
				'modified' => '2009-04-06 11:06:54',
			),
			array(
				'id' => '5',
				'parent_id' => '0',
				'libelle' => 'Service assemblée',
				'actif' => '1',
				'created' => '2009-04-06 11:07:03',
				'modified' => '2009-04-06 11:07:03',
			),
		);
	}

?>