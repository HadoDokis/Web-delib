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

	var $displayField = 'nom';

	var $validate = array(
		'nom' => VALID_NOT_EMPTY,
		'def_compteur' => VALID_NOT_EMPTY,
		'sequence_id' => VALID_NOT_EMPTY
	);

	var $belongsTo = 'Sequence';

	var $hasMany = 'Typeseance';

	var $cacheQueries = false;

	function validates()
	{
		// unicité du nom
		$this->isUnique('nom', $this->data['Compteur']['nom'], $this->data['Compteur']['id']);

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

}
?>