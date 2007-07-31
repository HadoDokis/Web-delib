<?php
class Commentaire extends AppModel {

	var $name = 'Commentaire';
	var $validate = array(
		'delib_id' => VALID_NOT_EMPTY,
		'agent_id' => VALID_NOT_EMPTY,
		'texte' => VALID_NOT_EMPTY,
	);

}
?>