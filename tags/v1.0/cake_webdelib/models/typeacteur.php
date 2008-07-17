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

class Typeacteur extends AppModel
{
	var $name = 'Typeacteur';

	var $displayField = "nom";

	var $validate = array('nom' => VALID_NOT_EMPTY);

	var $hasMany = 'Acteur';

	function validates()
	{
		// unicité du nom
		$this->isUnique('nom', $this->data['Typeacteur']['nom'], $this->data['Typeacteur']['id']);

		// choix elu/non elu fait
		if (!array_key_exists('elu', $this->data['Typeacteur']))
            $this->invalidate('elu');


		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

	/* retourne le libellé correspondant au champ elu 1 : élu, 0 : non élu */
	function libelleElu($elu = null, $majuscule = false) {
		return $elu ? ($majuscule ? 'Elu':'élu') : ($majuscule ? 'Non élu':'non élu');
	}

}
?>