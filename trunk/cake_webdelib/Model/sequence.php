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
				'message' => 'Entrer un nom pour la s�quence'
			),
			array(
				'rule' => 'isUnique',
				'message' => 'Entrer un autre nom, celui-ci est d�j� utilis�.'
			)
		),
		'num_sequence' => array(
			'rule' => 'numeric',
			'allowEmpty' => true,
			'message' => 'Le num�ro de s�quence doit �tre un nombre.'
		)
	);

	var $hasMany = 'Compteur';

	var $cacheQueries = false;
}
?>
