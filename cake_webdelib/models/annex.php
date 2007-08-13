<?php
class Annex extends AppModel {

	var $name = 'Annex';
	var $validate = array(
		'delib_id' => VALID_NOT_EMPTY,
		'chemin' => VALID_NOT_EMPTY,
		'titre' => VALID_NOT_EMPTY,
		'reference_id' => VALID_NOT_EMPTY,
	);

}
?>