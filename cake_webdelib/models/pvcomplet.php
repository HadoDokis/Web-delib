<?php
class Pvcomplet extends AppModel {

	var $name = 'Pvcomplet';
	var $validate = array(
		'seance_id' => VALID_NOT_EMPTY,
		'chemin' => VALID_NOT_EMPTY,
	);

}
?>