<?php
class Localisation extends AppModel {

	var $name = 'Localisation';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);
}
?>