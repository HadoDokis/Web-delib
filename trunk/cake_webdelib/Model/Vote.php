<?php
	class Vote extends AppModel {

		var $name = 'Vote';
		var $belongsTo = array(
			'Acteur' => array('className' => 'Acteur',
				'foreignKey' => 'acteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'counterCache' => ''
			)
		);
	}
?>
