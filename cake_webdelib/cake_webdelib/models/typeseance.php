<?php
class Typeseance extends AppModel {

	var $name = 'Typeseance';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

}
?>