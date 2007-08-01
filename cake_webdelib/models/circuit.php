<?php
class Circuit extends AppModel {

	var $name = 'Circuit';
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

	var $displayField="libelle";
	
	var $recursive = 2;

	var $hasMany = array ('AgentsCircuit' => array('className'=>'AgentsCircuit'));

}
?>