<?php
class Profil extends AppModel {

	var $name = 'Profil';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

}
?>