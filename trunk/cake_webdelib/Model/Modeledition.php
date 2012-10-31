<?php
class Modeledition extends AppModel {

	var $name = 'Modeledition';
	var $displayField = 'modele';
        var $useTable = 'models';
	
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
