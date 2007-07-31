<?php
class Theme extends AppModel {

	var $name = 'Theme';
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

}
?>