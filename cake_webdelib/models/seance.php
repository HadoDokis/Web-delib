<?php
class Seance extends AppModel {

	var $name = 'Seance';
	var $validate = array(
		'type_id' => VALID_NOT_EMPTY,
	);

}
?>