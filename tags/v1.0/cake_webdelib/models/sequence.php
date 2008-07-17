<?php
/**
* Gestion des séquences utilisées par les compteurs paramétrables
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

class Sequence extends AppModel
{
	var $name = 'Sequence';

	var $displayField = "nom";

	var $validate = array('nom' => VALID_NOT_EMPTY);

	var $hasMany = 'Compteur';

	var $cacheQueries = false;

	function validates()
	{
		// unicité du nom
		$this->isUnique('nom', $this->data['Sequence']['nom'], $this->data['Sequence']['id']);

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

}
?>