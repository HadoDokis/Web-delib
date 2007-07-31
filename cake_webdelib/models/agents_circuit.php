<?php
class AgentsCircuit extends AppModel {

	var $name = 'AgentsCircuit';
	//var $primaryKey = 'agent_id';
	var $validate = array(
		'circuit_id' => VALID_NOT_EMPTY,
		'position' => VALID_NOT_EMPTY,
	);

	var $useTable="agents_circuits";
	
	var $belongsTo = array(
			'Circuit' =>
				array('className' => 'Circuit',
						'foreignKey' => 'circuit_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),

			'Agent' =>
				array('className' => 'Agent',
						'foreignKey' => 'agent_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Service' =>
				array('className' => 'Service',
						'foreignKey' => 'service_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				)

	);
}
?>