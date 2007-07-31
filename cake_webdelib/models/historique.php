<?php
class Historique extends AppModel {

	var $name = 'Historique';
	var $validate = array(
		'delib_id' => VALID_NOT_EMPTY,
		'circuit_id' => VALID_NOT_EMPTY,
		'position' => VALID_NOT_EMPTY,
		'flag_position' => VALID_NOT_EMPTY,
		'flag_valid' => VALID_NOT_EMPTY,
		'reception' => VALID_NOT_EMPTY,
		'traitement' => VALID_NOT_EMPTY,
	);

}
?>