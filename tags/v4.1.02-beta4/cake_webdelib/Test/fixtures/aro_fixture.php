<?php

	class AroFixture extends CakeTestFixture {
		var $name = 'Aro';
		var $table = 'aros';
		var $import = array( 'table' => 'aros', 'connection' => 'default', 'records' => false);
		var $records = array(
			array(
				'id' => '1',
				'parent_id' => null,
				'model' => 'Profil',
				'foreign_key' => '0',
				'alias' => 'Administrateur',
				'lft' => '1',
				'rght' => '4',
			),
			array(
				'id' => '2',
				'parent_id' => '1',
				'model' => 'Utilisateur',
				'foreign_key' => '1',
				'alias' => 'admin',
				'lft' => '21',
				'rght' => '22',
			),
			array(
				'id' => '4',
				'parent_id' => null,
				'model' => 'Profil',
				'foreign_key' => '0',
				'alias' => 'Profil:Défaut',
				'lft' => '5',
				'rght' => '6',
			),
			array(
				'id' => '5',
				'parent_id' => null,
				'model' => 'Profil',
				'foreign_key' => '0',
				'alias' => 'Profil:Rédacteur',
				'lft' => '7',
				'rght' => '10',
			),
			array(
				'id' => '6',
				'parent_id' => '5',
				'model' => 'Utilisateur',
				'foreign_key' => '2',
				'alias' => 'Utilisateur:redac',
				'lft' => '8',
				'rght' => '9',
			),
			array(
				'id' => '7',
				'parent_id' => null,
				'model' => 'Profil',
				'foreign_key' => '0',
				'alias' => 'Profil:Service assemblée',
				'lft' => '11',
				'rght' => '14',
			),
			array(
				'id' => '8',
				'parent_id' => '7',
				'model' => 'Utilisateur',
				'foreign_key' => '5',
				'alias' => 'Utilisateur:assemblee',
				'lft' => '12',
				'rght' => '13',
			),
			array(
				'id' => '9',
				'parent_id' => null,
				'model' => 'Profil',
				'foreign_key' => '0',
				'alias' => 'Profil:Valideur',
				'lft' => '15',
				'rght' => '20',
			),
			array(
				'id' => '10',
				'parent_id' => '9',
				'model' => 'Utilisateur',
				'foreign_key' => '3',
				'alias' => 'valideur',
				'lft' => '16',
				'rght' => '17',
			),
			array(
				'id' => '11',
				'parent_id' => '9',
				'model' => 'Utilisateur',
				'foreign_key' => '4',
				'alias' => 'Utilisateur:valideur2',
				'lft' => '18',
				'rght' => '19',
			),
		);
	}

?>