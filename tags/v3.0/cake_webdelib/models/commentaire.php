<?php
class Commentaire extends AppModel {

	var $name = 'Commentaire';
	
	var $validate = array(
		'texte' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer un commentaire.'
			)
		)
	);

}
?>
