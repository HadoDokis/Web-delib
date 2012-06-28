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

	class Infosuplistedef extends AppModel{
		var $name = 'Infosuplistedef';

		var $displayField = "nom";

		var $belongsTo = 'Infosupdef';

		var $validate = array(
			'nom' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Entrer un nom pour l\'�l�ment'
				)
			)
		);

		/* Intervertit l'ordre de l'�l�ment $id avec le suivant ou le pr�c�dent suivant $following */
		function invert($id = null, $following = true) {
			// Initialisations
			$gap = $following ? 1 : -1;

			// lecture de l'�l�ment � d�placer
			$recFrom = $this->find('id = '.$id, 'id, ordre, infosupdef_id', null, -1);

			// lecture de l'�l�ment a intervertir
			$recTo = $this->find('infosupdef_id = '.$recFrom['Infosuplistedef']['infosupdef_id'].' AND actif = 1 AND ordre = '.($recFrom['Infosuplistedef']['ordre'] + $gap), 'id, ordre', null, -1);

			// Si pas d'�l�ment � intervertir alors on sort sans rien faire
			if (empty($recTo)) return;

			// Mise � jour du champ ordre pour les deux enregistrements
			$recFrom['Infosuplistedef']['ordre'] += $gap;
			$this->save($recFrom, false);
			$recTo['Infosuplistedef']['ordre'] -= $gap;
			$this->save($recTo, false);

			return;
		}

		function beforeSave() {
			/* calcul du n� d'ordre en cas d'ajout */
			if (!array_key_exists('id', $this->data['Infosuplistedef']) ||
				empty($this->data['Infosuplistedef']['id']))
				$this->data['Infosuplistedef']['ordre'] = $this->findCount('actif = 1 AND infosupdef_id = '.$this->data['Infosuplistedef']['infosupdef_id'], -1) + 1;

			return true;
		}

		/**
		 * R�ordonne les num�ros d'ordre apr�s une suppression pour l'infosupdef $infosupdefId
		 */
		function reOrdonne($infosupdefId) {

			$recs = $this->findAll('actif = 1 AND infosupdef_id = '.$infosupdefId, 'id, ordre', 'ordre', null, 1, -1);

			foreach($recs as $n=>$rec) {
				if (($n+1) != $rec['Infosuplistedef']['ordre']) {
					$rec['Infosuplistedef']['ordre'] = ($n+1);
					$this->save($rec, false);
				}
			}
		}

		/**
		 * Suppression de tous les �l�ments de l'infosupdef $infosupdefId
		 */
		function delList($infosupdefId) {
			$recs = $this->find('all', array('conditions'=>array('infosupdef_id' => $infosupdefId), 'fields'=>array('id'), 'recursive'=>-1));
			foreach($recs as $rec) {
					$this->delete($rec['Infosuplistedef']['id']);
			}
		}


	}
?>
