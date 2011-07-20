<?php
class Model extends AppModel {

	var $name = 'Model';
	var $displayField = 'modele';
	
	var $validate = array(
		'modele' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libellé.'
			)
		),
		'content' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer la délibération.'
			)
		)
	);
}
?>
