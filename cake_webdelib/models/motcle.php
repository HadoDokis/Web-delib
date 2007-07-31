<?php
class Motcle extends AppModel {

	var $name = 'Motcle';
	var $validate = array(
		'mot' => VALID_NOT_EMPTY,
	);

}
?>