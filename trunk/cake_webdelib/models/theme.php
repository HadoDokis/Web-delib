<?php
class Theme extends AppModel {

	var $name = 'Theme';
	var $displayField = "libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

}
?>