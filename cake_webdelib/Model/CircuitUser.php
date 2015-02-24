<?php
class CircuitUser extends AppModel {

	var $name = 'CircuitUser';
        var $useTable="circuits_users";
        
	var $belongsTo = array(
			'User' =>
				array('className' => 'User',
						'foreignKey' => 'user_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Circuit' =>
				array('className' => 'cakeflow.Circuit',
						'foreignKey' => 'circuit_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				)

	);
}