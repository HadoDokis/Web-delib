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
		'Deliberation' =>array('className'    => 'Deliberation',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    => false,
                        'foreignKey'   => 'foreign_key'),
		'Infosupdef'
	);

/**
 * Transforme la structure [0]['id']['deliberation_id']... en ['code_infosup']=>valeur, ...
 * Pour les infosup de type 'list', la paramètre $retIdEleListe permet de retourner soit l'id de l'élément de la liste soit sa valeur
 */
	function compacte($infosups = array(), $retIdEleListe = true) {
		$ret = array();

		foreach($infosups as $infosup) {
			$infosupdef = $this->Infosupdef->find( 'first' , array('conditions' => array( "Infosupdef.id" =>$infosup['infosupdef_id']), 
                                                                               'fields'     => array('code', 'type'),
                                                                               'recursive'  => -1));
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
					$ele = $this->Infosupdef->Infosuplistedef->find('first', array('conditions' => array('id' =>$infosup['text']), 
                                                                                                        'fields'    => array('nom'), 
                                                                                                        'recursive' => -1));
					$ret[$infosupdef['Infosupdef']['code']] = $ele['Infosuplistedef']['nom'];
				}
			}
		}

		return $ret;
	}

	/* sauvegarde les info sup. recues sous la forme ['code_infosup']=>valeur, ... */
	function saveCompacted($infosups, $foreignKey, $model) {
		foreach($infosups as $code=>$valeur) {
			// lecture de la définition de l'info sup
			$infosupdef = $this->Infosupdef->find('first', array(
				'recursive' => -1,
				'fields' => array('id', 'type'),
				'conditions' => array('code' => $code, 'model'=>$model)));

			// lecture de l'infosup en base
			$infosup = $this->find('first', array(
				'recursive' => -1,
				'fields' => array('id', 'foreign_key', 'model', 'infosupdef_id', 'file_name'),
				'conditions' => array(
					'foreign_key' => $foreignKey,
					'infosupdef_id' => $infosupdef['Infosupdef']['id'])));

			// si elle n'existe pas : création d'un nouveau et initialisation
			if (empty($infosup)) {
				$this->create();
				$infosup['Infosup']['foreign_key'] = $foreignKey;
				$infosup['Infosup']['infosupdef_id'] = $infosupdef['Infosupdef']['id'];
				$infosup['Infosup']['model'] = $model;
			}

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
					$modelRep = ($model == 'Deliberation') ? 'projet' : 'seance';
					$repDest = WWW_ROOT.'files'.DS.'generee'.DS.$modelRep.DS.$foreignKey.DS;
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
  * correspondant à $recherches qui est sous la forme array('infosupdef_id'=>'valeur')
  */
	function selectInfosup($recherches) {
		// initialisations
		$ret = '';
		$iAlias = 0;
		$from = '';
		$condition = '';
		$jointure = '';
		$repSelect = array();
		// construction des différentes clauses
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
			// construction et exécution de la requête
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

         function addField($champs,  $id, $model='Deliberation') {
            $champs_def = $this->Infosupdef->read(null, $champs['infosupdef_id']);
            if(($champs_def['Infosupdef']['type'] == 'list' )&&($champs['text']!= "")) {
                $tmp= $this->Infosupdef->Infosuplistedef->find('first', array('conditions' => array('Infosuplistedef.id' => $champs['text']),
                                                                              'fields'     => array('Infosuplistedef.nom'),
                                                                              'recursive'  => -1));
                $champs['text'] = $tmp['Infosuplistedef']['nom'];
            }
            elseif (($champs_def['Infosupdef']['type'] == 'list' )&&($champs['text']== ""))
                 return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode(' '), 'text'));
            if ($champs['text'] != null) {
                return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode($champs['text']), 'text'));
            }
            elseif ($champs['date'] != null) {
                include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/DateComponent.php');
                $this->Date = new DateComponent;
                return  (new GDO_FieldType($champs_def['Infosupdef']['code'], $this->Date->frDate($champs['date']),   'date'));
             }
             elseif ($champs['file_size'] != 0 ) {
                 $name = utf8_decode(str_replace(" ", "_", $champs['file_name']));
                 return (new GDO_ContentType($champs_def['Infosupdef']['code'], $name  ,'application/vnd.oasis.opendocument.text',  'binary', $champs['content']));
             }
             elseif ((!empty($champs['content'])) && ($champs['file_size']==0) ) {
                 include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/GedoooComponent.php');
                 include_once (ROOT.DS.APP_DIR.DS.'Controller/Component/ConversionComponent.php');
                 $this->Gedooo = new GedoooComponent;
                 $this->Conversion = new ConversionComponent;

                 if ( $model == 'Deliberation' ) {
                     $filename = WEBROOT_PATH."/files/generee/projet/$id/".$champs_def['Infosupdef']['code'].".html";
                     $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/projet/$id/", $champs_def['Infosupdef']['code'].".html", $champs['content']);
                     $content = $this->Conversion->convertirFichier($filename, "odt");
                     return (new GDO_ContentType($champs_def['Infosupdef']['code'], $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));
                 }
                 elseif ( $model == 'Seance' ) {
                     $filename = WEBROOT_PATH."/files/generee/seance/$id/".$champs_def['Infosupdef']['code'].".html";
                     $this->Gedooo->createFile(WEBROOT_PATH."/files/generee/seance/$id/", $champs_def['Infosupdef']['code'].".html", $champs['content']);
                     $content = $this->Conversion->convertirFichier($filename, "odt");
                     return (new GDO_ContentType($champs_def['Infosupdef']['code'], $filename, 'application/vnd.oasis.opendocument.text', 'binary', $content));

                 }
             }
            elseif  ($champs['text'] == '' )
                 return (new GDO_FieldType($champs_def['Infosupdef']['code'],  utf8_encode(' '), 'text'));
        }
}
?>
