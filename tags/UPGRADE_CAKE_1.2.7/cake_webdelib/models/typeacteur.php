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
	
	var $validate = array(
		'nom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer un nom pour le type d\'acteur'
			),
			array(
				'rule' => 'isUnique',
				'message' => 'Entrer un autre nom, celui-ci est déjà utilisé.'
			)
		),
		'elu' => array(
			'rule' => 'notEmpty',
			'message' => 'Choisir un statut (élu ou non élu)'
		)
	);

	var $hasMany = 'Acteur';

	/* retourne le libellé correspondant au champ elu 1 : élu, 0 : non élu */
	function libelleElu($elu = null, $majuscule = false) {
		return $elu ? ($majuscule ? 'Elu':'élu') : ($majuscule ? 'Non élu':'non élu');
	}

}
?>
