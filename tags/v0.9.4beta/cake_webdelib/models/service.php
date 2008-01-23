<?php
class Service extends AppModel {

	var $name = 'Service';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);
	var $hasMany = 'User';

}
?>