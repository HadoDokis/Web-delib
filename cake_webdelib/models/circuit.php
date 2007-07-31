<?php
class Circuit extends AppModel {

	var $name = 'Circuit';
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

	var $displayField="libelle";
	
	var $recursive = 2;

	var $hasMany = array ('AgentsCircuit' => array('className'=>'AgentsCircuit'));

//	var $hasAndBelongsToMany=array('AgentsCircuit' => array('className' => 'AgentsCircuit',
//						'joinTable' => 'agents_circuits',
//						'foreignKey' => 'agent_id',
//						'associationForeignKey' => 'circuit_id',
//						'conditions' => '',
//						'fields' => '',
//						'order' => '',
//						'limit' => '',
//						'offset' => '',
//						'unique' => '',
//						'finderQuery' => '',
//						'deleteQuery' => '',
//						'insertQuery' => ''));		
//	

}
?>