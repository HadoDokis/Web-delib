<?php
class Odj extends AppModel {

	var $name = 'Odj';
	var $validate = array(
		'seance_id' => VALID_NOT_EMPTY,
	);

}
?>