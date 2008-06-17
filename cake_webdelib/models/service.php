<?php
class Service extends AppModel {

	var $name = 'Service';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

	var $hasAndBelongsToMany = array(
		'User' => array(
			'classname'=>'User',
			'joinTable'=>'users_services',
			'foreignKey'=>'service_id',
			'associationForeignKey'=>'user_id',
			'conditions'=>'',
			'order'=>'',
			'limit'=>'',
			'unique'=>true,
			'finderQuery'=>'',
			'deleteQuery'=>''),
		'Acteur' => array(
			'classname'=>'Acteur',
			'joinTable'=>'acteurs_services',
			'foreignKey'=>'service_id',
			'associationForeignKey'=>'acteur_id',
			'conditions'=>'',
			'order'=>'',
			'limit'=>'',
			'unique'=>true,
			'finderQuery'=>'',
			'deleteQuery'=>'')
	);

}
?>