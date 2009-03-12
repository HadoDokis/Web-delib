<?php
/**
* Informations supplémentaires paramétrables des projets de délibération
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

	/* transforme la structure [0]['id']['deliberation_id']... en ['code_infosup']=>valeur, ... */
	function compacte($infosups = array()) {
		$ret = array();

		foreach($infosups as $infosup) {
			$infosupdef = $this->Infosupdef->find('id = '.$infosup['infosupdef_id'], 'code, type', null, -1);
			if ($infosupdef['Infosupdef']['type'] == 'text') {
				$ret[$infosupdef['Infosupdef']['code']] =  $infosup['text'];
			} elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				if (empty($infosup['date']) || $infosup['date'] == '0000-00-00')
					$ret[$infosupdef['Infosupdef']['code']] = '';
				else {
					$date = explode('-', $infosup['date']);
					$ret[$infosupdef['Infosupdef']['code']] = $date[2].'/'.$date[1].'/'.$date[0];
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'file') {
				$ret[$infosupdef['Infosupdef']['code']] = $infosup['file_name'];
			} elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
				$ret[$infosupdef['Infosupdef']['code']] = $infosup['content'];
			}
		}

		return $ret;
	}

	/* sauvegarde les info sup. recues sous la forme ['code_infosup']=>valeur, ... */
	function saveCompacted($infosups, $delib_id) {
		foreach($infosups as $code=>$valeur) {
			/* lecture de la définition de l'info sup */
			$infosupdef = $this->Infosupdef->find('code = \''.$code.'\'', 'id, type', null, -1);

			/* lecture de l'infosup en base */
			$infosup = $this->find('deliberation_id = '.$delib_id.' AND infosupdef_id = '.$infosupdef['Infosupdef']['id'], null, null,-1);
			/* si elle n'existe pas : création d'un nouveau et initialisation */
			if (empty($infosup)) {
				$this->create();
				$infosup['Infosup']['deliberation_id'] = $delib_id;
				$infosup['Infosup']['infosupdef_id'] = $infosupdef['Infosupdef']['id'];
			}

			/* affectation de la valeur en fonction du type */
			if ($infosupdef['Infosupdef']['type'] == 'text') {
				$infosup['Infosup']['text'] = $valeur;
			} elseif ($infosupdef['Infosupdef']['type'] == 'date') {
				$date = explode('/', $valeur);
				if (count($date) == 3)
					$infosup['Infosup']['date'] = $date[2].'-'.$date[1].'-'.$date[0];
				else
					$infosup['Infosup']['date'] = '';
			} elseif ($infosupdef['Infosupdef']['type'] == 'file') {
				$infosup['Infosup']['file_name'] = $valeur['name'];
				$infosup['Infosup']['file_size'] = $valeur['size'];
				$infosup['Infosup']['file_type'] = $valeur['type'];
				if (empty($valeur['tmp_name']))
					$infosup['Infosup']['content'] = '';
				else
					$infosup['Infosup']['content'] = fread(fopen($valeur['tmp_name'], "r"), $valeur['size']);
			} elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
				$infosup['Infosup']['content'] = $valeur;
			}

			/* Sauvegarde de l'info sup */
			$this->save($infosup);
		}
	}

}
?>