<?php
class SeancesUser extends AppModel {

	var $name = 'SeancesUser';
	var $useTable="seances_users";
	var $belongsTo = array(
			'Seance' =>
				array('className' => 'Seance',
						'foreignKey' => 'seance_id',
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
				)

	);
}
?>