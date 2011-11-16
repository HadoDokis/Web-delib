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
* * @license
*/

class Sequence extends AppModel
{
	var $name = 'Sequence';

	var $displayField = "nom";
	
	var $validate = array(
		'nom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer un nom pour la séquence'
			),
			array(
				'rule' => 'isUnique',
				'message' => 'Entrer un autre nom, celui-ci est déjà utilisé.'
			)
		),
		'num_sequence' => array(
			'rule' => 'numeric',
			'allowEmpty' => true,
			'message' => 'Le numéro de séquence doit être un nombre.'
		)
	);

	var $hasMany = 'Compteur';

	var $cacheQueries = false;
}
?>
