<?php
class Theme extends AppModel {

	var $name = 'Theme';
	
	var $displayField = "libelle";
	
	var $actsAs = array('Tree');
	
	var $validate = array(
		'libelle' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libellé.'
			)
		)
	);

}
?>
