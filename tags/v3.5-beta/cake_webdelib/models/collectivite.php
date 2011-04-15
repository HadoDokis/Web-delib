<?php
class Collectivite extends AppModel {
	var $name = 'Collectivite';
	var $cacheSources = 'false';
	
	var $validate = array(
		'nom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le nom de la collectivité'
			)
		),
		'adresse' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer l\'adresse.'
			)
		),
		'CP' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le code postal.'
			)
		),
		'ville' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer la ville.'
			)
		),
		'telephone' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le numéro de téléphone.'
			)
		)
	);
}
?>
