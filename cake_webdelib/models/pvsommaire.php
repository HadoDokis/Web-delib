<?php
class Pvsommaire extends AppModel {

	var $name = 'Pvsommaire';
	var $validate = array(
		'seance_id' => VALID_NOT_EMPTY,
		'chemin' => VALID_NOT_EMPTY,
	);

}
?>