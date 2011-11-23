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

	var $hasMany = array(
		'Infosup'=> array ( 'className'    => 'Infosup',
                                    'conditions'   => '',
                                    'order'        => '',
                                   'dependent'    => false,
                                   'foreignKey'   => 'foreign_key'),
		'Infosuplistedef'
	);
	
	var $validate = array(
		'nom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer un nom pour l\'information suppl�mentaire'
			),
			array(
				'rule' => 'isUnique',
				'message' => 'Entrer un autre nom, celui-ci est d�j� utilis�.'
			)
		),
		'type' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Selectionner un type'
			)
		),
		'code' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer un code pour l\'information suppl�mentaire'
			),
			array(
				'rule' => 'isUnique',
				'message' => 'Entrer un autre code, celui-ci est d�j� utilis�.'
			),
			array(
				'rule' => 'non_conforme_code',
				'message' => 'Le code est non conforme, essayer celui ci : %s'
			)
		)
	);

	var $types = array(
		'text' => 'Texte',
		'richText' => 'Texte enrichi',
		'date' => 'Date',
		'file' => 'Fichier',
		'boolean' => 'Bool�en',
		'odtFile' => 'Fichier ODT',
		'list' => 'Liste'
	);

	var $listSelectBoolean = array(
		'1' => 'Oui',
		'0' => 'Non'
	);

	var $listEditBoolean = array(
		'0' => 'd�coch�',
		'1' => 'coch�'
	);
	
	/**
	* FIXME: faire plus g�n�rique, car les r�gles d'un champ sont soit dans un
	* 	array (r�gle unique pour un champ), soit dans un array d'array (r�gles
	*	multiples pour un champ).
	*/

	function beforeValidate() {
		$codepropose = Inflector::variable($this->data['Infosupdef']['code']);

		foreach( $this->validate as $field => $rules ) {
			foreach( $rules as $key => $rule ) {
				if( $rule['rule'] == 'non_conforme_code' ) {
					$this->validate[$field][$key]['message'] = sprintf( $rule['message'], $codepropose );
				}
			}
		}
	}
	
	function non_conforme_code() {
		return $this->data['Infosupdef']['code'] == Inflector::variable($this->data['Infosupdef']['code']);
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
		if ($this->Infosup->find('first', array('conditions'=>array('infosupdef_id' => $aSupprimer['Infosupdef']['id']), 'recursive' => -1))) {
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

	function beforeSave() {
		/* valeur par defaut pour la taille du champ input lors de la saisie */
		if (isset($this->data['Infosupdef']['type']) && $this->data['Infosupdef']['type'] == 'text' && empty($this->data['Infosupdef']['taille']))
			$this->data['Infosupdef']['taille'] = 20;

		/* calcul du n� d'ordre en cas d'ajout */
		if (!array_key_exists('id', $this->data['Infosupdef']) ||
			empty($this->data['Infosupdef']['id']))
			$this->data['Infosupdef']['ordre'] = $this->findCount(null, -1) + 1;

		/* pas de recherche possible pour les infosup de type fichier et fichier odt */
		if (isset($this->data['Infosupdef']['type']) && ($this->data['Infosupdef']['type'] == 'file' || $this->data['Infosupdef']['type'] == 'odtFile')) {
			$this->data['Infosupdef']['recherche'] = 0;
			$this->data['Infosupdef']['val_initiale'] = '';
		}

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

/**
 * retourne un tableau ['code']['val_init'] des valeurs initiales des infosup
 */
	function valeursInitiales() {
		$ret = array();

		$recs = $this->findAll(null, 'code, val_initiale', 'ordre', null, 1, -1);
		foreach($recs as $rec) {
			if (!empty($rec['Infosupdef']['val_initiale']))
				$ret[$rec['Infosupdef']['code']] = $rec['Infosupdef']['val_initiale'];
		}

		return $ret;
	}

/**
 * Retourne les �l�ments des infosup de type 'list'
 */
	function generateListes($model) {
		$ret = array();

		$recs = $this->find('all', array('conditions' => array("type"  => 'list',
								     'model' => $model),
						 'fields'     => array('id', 'code'),
                                                 'order'      => 'ordre', 
                                                 'recursive'  => -1));
		foreach($recs as $rec) {
			$ret[$rec['Infosupdef']['code']] = $this->Infosuplistedef->find('list',array('conditions'=>array('actif' => '1','infosupdef_id' => $rec['Infosupdef']['id']),'order'=>array('ordre')));
		}

		return $ret;
	}

}
?>
