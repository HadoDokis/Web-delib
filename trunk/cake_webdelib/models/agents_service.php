<?php
class AgentsService extends AppModel {

	var $name = 'AgentsService';
	var $useTable="agents_services";
	var $belongsTo = array(
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