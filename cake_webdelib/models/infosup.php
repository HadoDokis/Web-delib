<?php
/**
* Informations suppl�mentaires param�trables des projets de d�lib�ration
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

class Infosup extends AppModel
{
	var $name = 'Infosup';

	var $belongsTo = array(
		'Deliberation',
		'Infosupdef'
	);

/**
 * Transforme la structure [0]['id']['deliberation_id']... en ['code_infosup']=>valeur, ...
 * Pour les infosup de type 'list', la param�tre $retIdEleListe permet de retourner soit l'id de l'�l�ment de la liste soit sa valeur
 */
	function compacte($infosups = array(), $retIdEleListe = true) {
		$ret = array();

		foreach($infosups as $infosup) {
			$infosupdef = $this->Infosupdef->find(" id = ".$infosup['infosupdef_id'], 'code, type', null, -1);
			if ($infosupdef['Infosupdef']['type'] == 'text') {
				$ret[$infosupdef['Infosupdef']['code']] = $infosup['text'];
			} elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
				$ret[$infosupdef['Infosupdef']['code']] = stripslashes(html_entity_decode($infosup['content']));
			} elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				if (empty($infosup['date']) || $infosup['date'] == '0000-00-00')
					$ret[$infosupdef['Infosupdef']['code']] = '';
				else {
					$date = explode('-', $infosup['date']);
					$ret[$infosupdef['Infosupdef']['code']] = $date[2].'/'.$date[1].'/'.$date[0];
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'file' ||
					$infosupdef['Infosupdef']['type'] == 'odtFile' ) {
				$ret[$infosupdef['Infosupdef']['code']] = $infosup['file_name'];
			} elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
				$ret[$infosupdef['Infosupdef']['code']] =  $infosup['text'];
			} elseif ($infosupdef['Infosupdef']['type'] == 'list') {
				if ($retIdEleListe || empty($infosup['text']))
					$ret[$infosupdef['Infosupdef']['code']] = $infosup['text'];
				else {
					$ele = $this->Infosupdef->Infosuplistedef->find('id = '.$infosup['text'], 'nom', null, -1);
					$ret[$infosupdef['Infosupdef']['code']] = $ele['Infosuplistedef']['nom'];
				}
			}
		}

		return $ret;
	}

	/* sauvegarde les info sup. recues sous la forme ['code_infosup']=>valeur, ... */
	function saveCompacted($infosups, $delib_id, $model) {
		foreach($infosups as $code=>$valeur) {
			// lecture de la d�finition de l'info sup
			$infosupdef = $this->Infosupdef->find('first', array(
				'recursive' => -1,
				'fields' => array('id', 'type'),
				'conditions' => array('code' => $code)));

			// lecture de l'infosup en base
			$infosup = $this->find('first', array(
				'recursive' => -1,
				'fields' => array('id', 'foreign_key', 'model', 'infosupdef_id', 'file_name'),
				'conditions' => array(
					'foreign_key' => $delib_id,
					'infosupdef_id' => $infosupdef['Infosupdef']['id'])));

			// si elle n'existe pas : cr�ation d'un nouveau et initialisation
			if (empty($infosup)) {
				$this->create();
				$infosup['Infosup']['foreign_key'] = $delib_id;
				$infosup['Infosup']['infosupdef_id'] = $infosupdef['Infosupdef']['id'];
			}
				$infosup['Infosup']['model'] = $model;

			// affectation de la valeur en fonction du type
			switch($infosupdef['Infosupdef']['type']) {
				case 'text' :
				case 'boolean' :
				case 'list' :
					$infosup['Infosup']['text'] = $valeur;
					break;
				case 'richText' :
					$infosup['Infosup']['content'] = $valeur;
					break;
				case 'date' :
					$date = explode('/', $valeur);
					if (count($date) == 3)
						$infosup['Infosup']['date'] = $date[2].'-'.$date[1].'-'.$date[0];
					else
						$infosup['Infosup']['date'] = '';
					break;
				case 'file' :
				case 'odtFile' :
					$repDest = WWW_ROOT.'files'.DS.'generee'.DS.'projet'.DS.$delib_id.DS;
					if (empty($valeur['tmp_name'])) {
						if (isset($infosup['Infosup']['file_name']) && is_file($repDest.$infosup['Infosup']['file_name']))
							@unlink($repDest.$infosup['Infosup']['file_name']);
						$infosup['Infosup']['file_name'] = '';
						$infosup['Infosup']['file_size'] = 0;
						$infosup['Infosup']['file_type'] = '';
						$infosup['Infosup']['content'] = '';
					} elseif (file_exists($valeur['tmp_name'])) {
						if (isset($infosup['Infosup']['file_name']) && ($infosup['Infosup']['file_name'] != $valeur['name']) && is_file($repDest.$infosup['Infosup']['file_name']))
							@unlink($repDest.$infosup['Infosup']['file_name']);
						$infosup['Infosup']['file_name'] = $valeur['name'];
						$infosup['Infosup']['file_size'] = $valeur['size'];
						$infosup['Infosup']['file_type'] = $valeur['type'];
						$infosup['Infosup']['content'] = fread(fopen($valeur['tmp_name'], "r"), $valeur['size']);
						if ($infosupdef['Infosupdef']['type'] == 'odtFile') {
							if (!is_dir($repDest)) mkdir($repDest, 0770, true);
							move_uploaded_file($valeur['tmp_name'], $repDest.$valeur['name']);
						}
					} elseif (!empty($valeur)) {
						$infosup['Infosup']['file_name'] = $code;
						$infosup['Infosup']['file_size'] = strlen($valeur);
						$infosup['Infosup']['content']= $valeur;
					}
					break;
			}

			// Sauvegarde de l'info sup
			$this->save($infosup);
		}
	}

 /*
  * Retourne la liste des deliberation_id sous la forme 'delib_id1, delib_id2, ...'
  * correspondant � $recherches qui est sous la forme array('infosupdef_id'=>'valeur')
  */
	function selectInfosup($recherches) {
		// initialisations
		$ret = '';
		$iAlias = 0;
		$from = '';
		$condition = '';
		$jointure = '';
		$repSelect = array();
		// construction des diff�rentes clauses
		foreach($recherches as $infosupdefId => $recherche) {
			if (strlen(trim($recherche))) {
				$infosupType = $this->Infosupdef->field('type', "id = $infosupdefId");
				$iAlias++;
				$alias = 'infosups'.$iAlias;
				$from .= (empty($from) ? '' : ', ') . 'infosups ' . $alias;
				$jointure.= ($iAlias > 1) ? "infosups1.foreign_key = $alias.foreign_key AND " : '';
				if ($infosupType == 'text') {
					$champRecherche = $alias.'.text';
					$operateurRecherche = (strpos($recherche, '%')===false) ? '=' : 'like';
				} elseif ($infosupType == 'richText') {
					$champRecherche = $alias.'.content';
					$operateurRecherche = (strpos($recherche, '%')===false) ? '=' : 'like';
				} elseif ($infosupType == 'date') {
					$champRecherche = $alias.'.date';
					$operateurRecherche = '=';
					$temp = explode('/', $recherche);
		    		$recherche = $temp[2].'-'.$temp[1].'-'.$temp[0];
				} elseif ($infosupType == 'boolean') {
					$champRecherche = $alias.'.text';
					$operateurRecherche = '=';
				} elseif ($infosupType == 'list') {
					$champRecherche = $alias.'.text';
					$operateurRecherche = '=';
				}
				$condition .= (empty($condition) ? '' : ' AND ') . "($alias.infosupdef_id = $infosupdefId AND $champRecherche $operateurRecherche '$recherche')";
			}
		}
		if ($iAlias) {
			// construction et ex�cution de la requ�te
			$select = 'select infosups1.foreign_key ';
			$select .= 'from ' . $from . ' ';
			$select .= 'where ' . $jointure . $condition;
			$repSelect = $this->query($select);
			if (empty($repSelect))
				$ret = '-1';
			else {
				foreach($repSelect as $infosup)
					$ret .= (empty($ret) ? '' : ', ') .$infosup['infosups1']['foreign_key'];
			}
		}

		return $ret;
	}
}
?>
