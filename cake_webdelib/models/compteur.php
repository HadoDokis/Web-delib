<?php
/**
* Gestion des compteurs paramétrables.
*
* PHP versions 4 and 5
* @filesource
* @copyright
* @link			http://www.adullact.org
* @package			web-delib
* @subpackage
* @since
* @version			1.0
* @modifiedby
* @lastmodified	$Date: 2007-10-14
* @license
*/

class Compteur extends AppModel
{
	var $name = 'Compteur';

	var $validate = array(
		'nom' => VALID_NOT_EMPTY,
		'defcompteur' => VALID_NOT_EMPTY
	);
}
?>