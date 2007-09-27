<?php
class Listepresence extends AppModel {

	var $name = 'Listepresence';
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