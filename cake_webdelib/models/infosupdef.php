<?php
/**
* �finitions des informations suppl�mentaires param�trables des projets de d�lib�ration
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

class Infosupdef extends AppModel
{
	var $name = 'Infosupdef';

	var $displayField = "nom";

	var $hasMany = 'Infosup';

	var $validate = array(
		'nom' => VALID_NOT_EMPTY,
		'type' => VALID_NOT_EMPTY,
		'code' => VALID_NOT_EMPTY
	);

	var $types = array(
		'text' => 'Texte',
		'richText' => 'Texte enrichi',
		'date' => 'Date',
		'file' => 'Fichier',
		'boolean' => 'Bool�en'
	);

	var $listSelectBoolean = array(
		'1' => 'Oui',
		'0' => 'Non'
	);

	function validates() {
		// unicit� du nom
		$this->isUnique('nom', $this->data['Infosupdef']['nom'], $this->data['Infosupdef']['id']);

		// unicit� du code
		$this->isUnique('code', $this->data['Infosupdef']['code'], $this->data['Infosupdef']['id']);

		// une infosup de type fichier ne peut pas faire l'objet d'une recherche
		if ($this->data['Infosupdef']['type'] == 'file' && $this->data['Infosupdef']['recherche'])
			$this->invalidate('rechercheIncompatibleAvecTypeFichier');

		$errors = $this->invalidFields();
		return count($errors) == 0;
	}

	/* retourne la liste code/libell� pour les types d'information */
	function generateListType() {
		return $this->types;
	}

	/* retourne le libell� correspondant au type $type */
	function libelleType($type) {
		return $this->types[$type];
	}

	/* retourne le libell� correspondant au bool�en $recherche */
	function libelleRecherche($recherche) {
		return $recherche ? 'Oui' : 'Non';
	}

	/* retourne true si l'instance $aSupprimer peut �tre supprim�e et false dans le cas contraire */
	/* documente la raison de la non suppression dans $message */
	function isDeletable($aSupprimer, &$message) {
		$infosup = $this->Infosup->find('infosupdef_id = '.$aSupprimer['Infosupdef']['id'], 'id', null, -1);
		if ($infosup) {
			$message = "L'information suppl&eacute;mentaire '".$aSupprimer['Infosupdef']['nom']."' est utilis&eacute;e dans au moins un projet : suppression impossible";
			return false;
		} else
			return true;
	}

	/* Intervertit l'ordre de l'�l�ment $id avec le suivant ou le pr�c�dent suivant $following */
	function invert($id = null, $following = true) {
		// Initialisations
		$gap = $following ? 1 : -1;

		// lecture de l'�l�ment � d�placer
		$recFrom = $this->find('id = '.$id, 'id, ordre', null, -1);

		// lecture de l'�l�ment a intervertir
		$recTo = $this->find('ordre = '.($recFrom['Infosupdef']['ordre'] + $gap), 'id, ordre', null, -1);

		// Si pas d'�l�ment � intervertir alors on sort sans rien faire
		if (empty($recTo)) return;

		// Mise � jour du champ ordre pour les deux enregistrements
		$recFrom['Infosupdef']['ordre'] += $gap;
		$this->save($recFrom, false);
		$recTo['Infosupdef']['ordre'] -= $gap;
		$this->save($recTo, false);

		return;
	}

	/* Initialise la valeur par d�faut pour la taille */
	/* Donne un num�ro d'ordre pour un nouvel enregistrement */
	function beforeSave() {
		/* valeur par defaut pour la taille du champ input lors de la saisie */
		if (array_key_exists('taille', $this->data['Infosupdef']) &&
			empty($this->data['Infosupdef']['taille']))
			$this->data['Infosupdef']['taille'] = 20;

		/* calcul du n� d'ordre en cas d'ajout */
		if (!array_key_exists('id', $this->data['Infosupdef']) ||
			empty($this->data['Infosupdef']['id']))
			$this->data['Infosupdef']['ordre'] = $this->findCount(null, -1) + 1;

		/* Camelisation du code */
		if (array_key_exists('code', $this->data['Infosupdef']))
			$this->data['Infosupdef']['code'] = Inflector::variable($this->data['Infosupdef']['code']);

		return true;
	}

	/* R�ordonne les num�ros d'ordre apr�s une suppression */
	function afterDelete() {

		$recs = $this->findAll(null, 'id, ordre', 'ordre', null, 1, -1);

		foreach($recs as $n=>$rec) {
			if (($n+1) != $rec['Infosupdef']['ordre']) {
				$rec['Infosupdef']['ordre'] = ($n+1);
				$this->save($rec, false);
			}
		}
	}

}
?>