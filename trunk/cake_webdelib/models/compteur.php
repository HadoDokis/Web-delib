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

	function validates() {
		// unicité du nom
		$this->isUnique('nom', $this->data['Compteur']['nom'], $this->data['Compteur']['id']);

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

/**
* Retourne la valeur suivante du compteur,
* enregistre la nouvelle valeur de la séquence et du critère de réinitialisation en base
*
* @param int $id Numéro de l'id du compteur
* @retourne string Valeur suivante du compteur
* @access public
*/
	function genereCompteur($id = null) {
		/* initialisations */
		if (!$id) {
			$id = $this->id;
		}

		/* initialisation du tableau de recherche et de remplacement pour la date */
		$timestamp = time();
		$remplaceD = array("#AAAA#" => date("Y", $timestamp)
			, "#AA#" => date("y", $timestamp)
			, "#M#" => date("n", $timestamp)
			, "#MM#" => date("m", $timestamp)
			, "#J#" => date("j", $timestamp)
			, "#JJ#" => date("d", $timestamp)
		);

		/* lecture du compteur en base */
		$cptEnBase = $this->read(null, $id);

		/* génération du critère de réinitialisation courant */
		$val_reinitCourant = str_replace(array_keys($remplaceD), array_values($remplaceD), $cptEnBase['Compteur']['def_reinit']);

		/* traitement de la séquence */
		if ($val_reinitCourant != $cptEnBase['Compteur']['val_reinit']) {
			$cptEnBase['Sequence']['num_sequence'] = 1;
			$cptEnBase['Compteur']['val_reinit'] = $val_reinitCourant;
    	} else
			$cptEnBase['Sequence']['num_sequence']++;

		/* initialisation du tableau de recherche et de remplacement pour la séquence */
		$strnseqS = sprintf("%'_10d", $cptEnBase['Sequence']['num_sequence']);
		$strnseqZ = sprintf("%010d", $cptEnBase['Sequence']['num_sequence']);

		$remplaceS = array("#s#" => $cptEnBase['Sequence']['num_sequence']
			, "#S#" => substr($strnseqS, -1, 1)
			, "#SS#" => substr($strnseqS, -2, 2)
			, "#SSS#" => substr($strnseqS, -3, 3)
			, "#SSSS#" => substr($strnseqS, -4, 4)
			, "#SSSSS#" => substr($strnseqS, -5, 5)
			, "#SSSSSS#" => substr($strnseqS, -6, 6)
			, "#SSSSSSS#" => substr($strnseqS, -7, 7)
			, "#SSSSSSSS#" => substr($strnseqS, -8, 8)
			, "#SSSSSSSSS#" => substr($strnseqS, -9, 9)
			, "#SSSSSSSSSS#" => $strnseqS
			, "#0#" => substr($strnseqZ, -1, 1)
			, "#00#" => substr($strnseqZ, -2, 2)
			, "#000#" => substr($strnseqZ, -3, 3)
			, "#0000#" => substr($strnseqZ, -4, 4)
			, "#00000#" => substr($strnseqZ, -5, 5)
			, "#000000#" => substr($strnseqZ, -6, 6)
			, "#0000000#" => substr($strnseqZ, -7, 7)
			, "#00000000#" => substr($strnseqZ, -8, 8)
			, "#000000000#" => substr($strnseqZ, -9, 9)
			, "#0000000000#" => $strnseqZ
		);

		/* génération de la valeur du compteur */
		$valCompteurD = str_replace(array_keys($remplaceD), array_values($remplaceD), $cptEnBase['Compteur']['def_compteur']);
		$valCompteur = str_replace(array_keys($remplaceS), array_values($remplaceS), $valCompteurD);

		/* Sauvegarde du compteur et de la séquence */
		$this->save($cptEnBase);
		$this->Sequence->save($cptEnBase);

		/* retourne la valeur du compteur générée */
		return $valCompteur;
	}

}
?>