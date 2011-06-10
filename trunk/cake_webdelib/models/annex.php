<?php
class Annex extends AppModel {

	var $name = 'Annex';
	var $displayField="titre";
	
	var $belongsTo = array(
		'Deliberation' => array(
			'foreignKey' => 'foreign_key',
			'conditions' => array('Annex.model' => 'Deliberation')
		)
	);  

}
?>
