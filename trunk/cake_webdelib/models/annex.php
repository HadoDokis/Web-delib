<?php
class Annex extends AppModel {

	var $name = 'Annex';
	var $displayField="titre";
	var $validate = array(
		'delib_id' => VALID_NOT_EMPTY,
//		'chemin' => VALID_NOT_EMPTY,
		'titre' => VALID_NOT_EMPTY,
//		'reference_id' => VALID_NOT_EMPTY,
	);
	
	var $belongsTo = "Deliberation";

}
?>
