<?php
class Model extends AppModel {

	var $name = 'Model';
	var $displayField = 'modele';
	
	var $validate = array(
		'modele' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libell�.'
			)
		),
		'content' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer la d�lib�ration.'
			)
		)
	);
}
?>
