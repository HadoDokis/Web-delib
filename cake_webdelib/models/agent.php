<?php
class Agent extends AppModel {

	var $name = 'Agent';
	var $validate = array(
		'login' => VALID_NOT_EMPTY,
		'password' => VALID_NOT_EMPTY,
		'nom' => VALID_NOT_EMPTY,
		'prenom' => VALID_NOT_EMPTY,
	);

	
	var $recursive = 2;
	var $displayField="nom";
	var $hasAndBelongsToMany=array('Service'=>array('classname'=>'Service',
													'joinTable'=>'agents_services',
													'foreignKey'=>'agent_id',
													'associationForeignKey'=>'service_id',
													'conditions'=>'',
													'order'=>'',
													'limit'=>'',
													'unique'=>true,
													'finderQuery'=>'',
													'deleteQuery'=>'')
													,
											'Circuit' => array('className' => 'Circuit',
						'joinTable' => 'agents_circuits',
						'foreignKey' => 'agent_id',
						'associationForeignKey' => 'circuit_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'unique' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => ''),		
						
									'Profil'=>array('classname'=>'Profil',
													'joinTable'=>'agents_profils',
													'foreignKey'=>'agent_id',
													'associationForeignKey'=>'profil_id',
													'conditions'=>'',
													'order'=>'',
													'limit'=>'',
													'unique'=>true,
													'finderQuery'=>'',
													'deleteQuery'=>'')
);
	
 	var $hasMany = array(
    'AgentsCircuit' =>
            array('className' => 'AgentsCircuit',
                 ),
    ); 
	
	
	
}
?>