<?php
class Typeseance extends AppModel {

	var $name = 'Typeseance';
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

}
?>