<?php
/**
* Gestion des s�quences utilis�es par les compteurs param�trables
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
				'message' => 'Entrer un autre nom, celui-ci est d�j� utilis�.'
			)
		),
		'elu' => array(
			'rule' => 'notEmpty',
			'message' => 'Choisir un statut (�lu ou non �lu)'
		)
	);

	var $hasMany = 'Acteur';

	/* retourne le libell� correspondant au champ elu 1 : �lu, 0 : non �lu */
	function libelleElu($elu = null, $majuscule = false) {
		return $elu ? ($majuscule ? 'Elu':'�lu') : ($majuscule ? 'Non �lu':'non �lu');
	}

}
?>
