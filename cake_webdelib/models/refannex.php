<?php
class Refannex extends AppModel {

	var $name = 'Refannex';
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

}
?>