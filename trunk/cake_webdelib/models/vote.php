<?php
class Vote extends AppModel {

	var $name = 'Vote';
	var $validate = array(
		'liste_id' => VALID_NOT_EMPTY,
		'seance_id' => VALID_NOT_EMPTY,
		'delib_id' => VALID_NOT_EMPTY,
	);

}
?>