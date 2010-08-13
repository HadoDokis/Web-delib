<?php
class Circuit extends AppModel {

	var $name = 'Circuit';

	var $validate = array(
		'libelle' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libell�.'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Ce libell� est d�j� utilis�.'
			)
		)
	);

	var $displayField="libelle";

	var $recursive = 2;

	var $hasMany = array ('UsersCircuit' => array('className'=>'UsersCircuit'));

}
?>
