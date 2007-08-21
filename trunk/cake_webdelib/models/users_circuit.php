<?php
class UsersCircuit extends AppModel {

	var $name = 'UsersCircuit';
	//var $primaryKey = 'user_id';
	var $validate = array(
		'circuit_id' => VALID_NOT_EMPTY,
		'position' => VALID_NOT_EMPTY,
	);

	var $useTable="users_circuits";
	
	var $belongsTo = array(
			'Circuit' =>
				array('className' => 'Circuit',
						'foreignKey' => 'circuit_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),

			'User' =>
				array('className' => 'User',
						'foreignKey' => 'user_id',
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