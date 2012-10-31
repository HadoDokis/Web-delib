<?php
class UsersService extends AppModel {

	var $name = 'UsersService';
	var $useTable="users_services";
	var $belongsTo = array(
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