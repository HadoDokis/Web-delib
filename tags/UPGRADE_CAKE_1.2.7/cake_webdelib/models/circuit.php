<?php
class Circuit extends AppModel {

	var $name = 'Circuit';

	var $validate = array(
		'libelle' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libellé.'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Ce libellé est déjà utilisé.'
			)
		)
	);

	var $displayField="libelle";

	var $recursive = 2;

	var $hasMany = array ('UsersCircuit' => array('className'=>'UsersCircuit'));

}
?>
