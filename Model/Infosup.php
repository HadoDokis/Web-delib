<?php
/**
 * Informations supplémentaires paramétrables des projets de délibération
 *
 * PHP versions 4 and 5
 * @link            http://www.adullact.org
 * @package        web-delib
 * @lastmodified    $Date: 2007-10-14
 */
App::uses('File', 'Utility');

class Infosup extends AppModel {

    public $belongsTo = array(
        'Deliberation' => array(
            'className' => 'Deliberation',
            'conditions' => '',
            'order' => '',
            'dependent' => false,
            'foreignKey' => 'foreign_key'),
        'Infosupdef'
    );

    public $validate = array(
        'file_name' => array(
            'rule' => array('maxLength', 255),
            'message' => 'Nom de fichier trop long (255 caractères maximum)', 'growl'
        ),
    );

    /**
     * Transforme la structure [0]['id']['deliberation_id']... en ['code_infosup']=>valeur, ...
     * Pour les infosup de type 'list', la paramètre $retIdEleListe permet de retourner soit l'id de l'élément de la liste soit sa valeur
     */
    function compacte($infosups = array(), $retIdEleListe = true) {
        $ret = array();

        foreach ($infosups as $infosup) {
            $infosupdef = $this->Infosupdef->find('first', array('conditions' => array("Infosupdef.id" => $infosup['infosupdef_id']),
                'fields' => array('code', 'type'),
                'recursive' => -1));
            if ($infosupdef['Infosupdef']['type'] == 'text') {
                $ret[$infosupdef['Infosupdef']['code']] = $infosup['text'];
            } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                $ret[$infosupdef['Infosupdef']['code']] = stripslashes(html_entity_decode($infosup['content']));
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                if (empty($infosup['date']) || $infosup['date'] == '0000-00-00')
                    $ret[$infosupdef['Infosupdef']['code']] = '';
                else {
                    $date = explode('-', $infosup['date']);
                    $ret[$infosupdef['Infosupdef']['code']] = $date[2] . '/' . $date[1] . '/' . $date[0];
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'file' || $infosupdef['Infosupdef']['type'] == 'odtFile') {
                $ret[$infosupdef['Infosupdef']['code']] = $infosup['file_name'];
            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                $ret[$infosupdef['Infosupdef']['code']] = $infosup['text'];
            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                if ($retIdEleListe || empty($infosup['text']))
                    $ret[$infosupdef['Infosupdef']['code']] = $infosup['text'];
                else {
                    $ele = $this->Infosupdef->Infosuplistedef->find('first', array('conditions' => array('id' => $infosup['text']),
                        'fields' => array('nom'),
                        'recursive' => -1));
                    $ret[$infosupdef['Infosupdef']['code']] = $ele['Infosuplistedef']['nom'];
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
                $ret[$infosupdef['Infosupdef']['code']] = array();
                if ($retIdEleListe || empty($infosup['text'])) {
                    foreach (explode(',', str_replace("'", '', $infosup['text'])) as $elt) {
                        if (!empty($elt))
                            $ret[$infosupdef['Infosupdef']['code']][] = $elt;
                    }
                } else {
                    $ids = explode(',', str_replace("'", '', $infosup['text']));
                    $elts = $this->Infosupdef->Infosuplistedef->find('all', array(
                        'conditions' => array('id' => $ids),
                        'fields' => array('nom'),
                        'recursive' => -1));
                    foreach ($elts as $elt) {
                        $ret[$infosupdef['Infosupdef']['code']][] = $elt['Infosuplistedef']['nom'];
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * sauvegarde les info sup. recues sous la forme ['code_infosup']=>valeur, ...
     * @param $infosups
     * @param $foreignKey
     * @param $model
     * @return bool
     */
    function saveCompacted(&$infosups, $foreignKey, $model) {
        $success = true;
        foreach ($infosups as $code => $valeur) {
            
            $validator = $this->validator();
            // Retire la règle 'required' de file_type
            if (isset($validator['text']))
                unset($validator['text']);
            if (isset($validator['file_type']))
                unset($validator['file_type']);

            // lecture de la définition de l'info sup
            $infosupdef = $this->Infosupdef->find('first', array(
                'recursive' => -1,
                'fields' => array('id', 'type','nom'),
                'conditions' => array('code' => $code, 'model' => $model)));

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
            } else {
                $this->id = $infosup['Infosup']['id'];
            }

            // affectation de la valeur en fonction du type
            switch ($infosupdef['Infosupdef']['type']) {
                case 'text' :
                case 'boolean' :
                case 'list' :
                    //debug($infosupdef['Infosupdef']['nom'].'=======>'.$valeur);
                    // Ajout de la regle de validation
                    if($infosupdef['Infosupdef']['type']==='text')
                        $this->validator()->add('text', 'required', array(
                            'rule' => array('between', 0, 255),
                            'message' => '\"'.$infosupdef['Infosupdef']['nom'].'\" trop long (255 caractères maximum)', 'growl',
                        ));
                    $infosup['Infosup']['text'] = $valeur;
                    break;
                case 'listmulti' :
                    if (!empty($valeur)) {
                        //Ajout de quotes
                        foreach ($valeur as &$val)
                            $val = "'$val'";
                        $infosup['Infosup']['text'] = implode(',', $valeur);
                    } else {
                        $infosup['Infosup']['text'] = '';
                    }
                    break;
                case 'richText' :
                    $infosup['Infosup']['content'] = $valeur;
                    break;
                case 'date' :
                    $date = explode('/', $valeur);
                    if (count($date) == 3)
                        $infosup['Infosup']['date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
                    else
                        $infosup['Infosup']['date'] = '';
                    break;
                case 'file' :
                case 'odtFile' :
                    $modelRep = ($model == 'Deliberation') ? 'projet' : 'seance';
                    $repDest = WWW_ROOT . 'files' . DS . 'generee' . DS . $modelRep . DS . $foreignKey . DS;
                    //Si import d'un nouveau fichier
                    if (is_array($infosups[$code]) && !empty($infosups[$code]['tmp_name'])) {

                        if ($infosupdef['Infosupdef']['type'] == 'odtFile')
                            $allowed = $this->checkFormat(array($code => $infosups[$code]), 'odt', true);
                        else
                            $allowed = $this->checkFormat(array($code => $infosups[$code]), null, true);

                        if ($allowed) {
                            $infosup['Infosup']['file_name'] = $infosups[$code]['name'];
                            $infosup['Infosup']['file_size'] = $infosups[$code]['size'];
                            $infosup['Infosup']['file_type'] = $infosups[$code]['type'];
                            $infosup['Infosup']['content'] = file_get_contents($infosups[$code]['tmp_name']);
                        }
                        else {
                            unset($infosups[$code]);
                            if ($infosupdef['Infosupdef']['type'] == 'odtFile')
                                $this->invalidate($code, 'Format de fichier invalide, Document ODT attendu');
                            else
                                $this->invalidate($code, 'Format de fichier non reconnu par l\'application');
                            $success = false;
                        }

                    } //On sauvegarde le fichier déjà present avec les modifications apportées
                    elseif (!empty($infosups[$code]) && is_string($infosups[$code])) {
                        $file = new File($repDest . $infosup['Infosup']['file_name'], false);
                        $infosup['Infosup']['file_size'] = $file->size();
                        $infosup['Infosup']['content'] = $file->read();
                        $file->close();
                    } //Si aucun fichier présent on vide la base
                    else {
                        if (isset($infosup['Infosup']['file_name']) && is_file($repDest . $infosup['Infosup']['file_name']))
                            @unlink($repDest . $infosup['Infosup']['file_name']);

                        $infosup['Infosup']['file_name'] = '';
                        $infosup['Infosup']['file_size'] = '';
                        $infosup['Infosup']['file_type'] = '';
                        $infosup['Infosup']['content'] = '';
                        unset($infosups[$code]);
                    }
                    break;
            }
            // Sauvegarde de l'info sup
            if ($success)
                $success &= $this->save($infosup);
        }
        return $success;
    }

    function checkMimetype($mimetype, $allowed_mimetypes) {
        return in_array($mimetype['file_type'], $allowed_mimetypes);
    }


    /*
     * Retourne la liste des deliberation_id sous la forme 'delib_id1, delib_id2, ...'
     * correspondant à $recherches qui est sous la forme array('infosupdef_id'=>'valeur')
     */
    function selectInfosup($recherches) {
        // initialisations
        $iAlias = 0;
        $from = '';
        $condition = '';
        $jointure = '';
        // construction des différentes clauses
        foreach ($recherches as $infosupdefId => $recherche) {
            if (is_array($recherche)) {
                $infosupType = $this->Infosupdef->field('type', "id = $infosupdefId");
                if ($infosupType == 'listmulti') {
                    $iAlias++;
                    $alias = 'infosups' . $iAlias;
                    $from .= (empty($from) ? '' : ', ') . 'infosups ' . $alias;
                    $jointure .= ($iAlias > 1) ? "infosups1.foreign_key = $alias.foreign_key AND " : '';
                    $champRecherche = $alias . '.text';
                    $rechercheValues = '';
                    foreach ($recherche as $idInfoSup) {
                        if (!empty($rechercheValues))
                            $rechercheValues .= ' AND';
                        $rechercheValues .= " $champRecherche LIKE '%''$idInfoSup''%'";
                    }
                    $condition .= (empty($condition) ? '' : ' AND ') . "($alias.infosupdef_id = $infosupdefId AND ($rechercheValues))";
                }
            } elseif (strlen(trim($recherche))) {
                $infosupType = $this->Infosupdef->field('type', "id = $infosupdefId");
                $iAlias++;
                $alias = 'infosups' . $iAlias;
                $from .= (empty($from) ? '' : ', ') . 'infosups ' . $alias;
                $jointure .= ($iAlias > 1) ? "infosups1.foreign_key = $alias.foreign_key AND " : '';
                if ($infosupType == 'text') {
                    $champRecherche = $alias . '.text';
                    $operateurRecherche = (strpos($recherche, '%') === false) ? '=' : 'like';
                } elseif ($infosupType == 'richText') {
                    $champRecherche = $alias . '.content';
                    $operateurRecherche = (strpos($recherche, '%') === false) ? '=' : 'like';
                } elseif ($infosupType == 'date') {
                    $champRecherche = $alias . '.date';
                    $operateurRecherche = '=';
                    $temp = explode('/', $recherche);
                    $recherche = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
                } elseif ($infosupType == 'boolean') {
                    $champRecherche = $alias . '.text';
                    $operateurRecherche = '=';
                } elseif ($infosupType == 'list') {
                    $champRecherche = $alias . '.text';
                    $operateurRecherche = '=';
                }
                $condition .= (empty($condition) ? '' : ' AND ') . "($alias.infosupdef_id = $infosupdefId AND $champRecherche $operateurRecherche '$recherche')";
            }
        }
        $resultIds = array();
        if ($iAlias) {
            // construction et exécution de la requête
            $select = 'select infosups1.foreign_key ';
            $select .= 'from ' . $from . ' ';
            $select .= 'where ' . $jointure . $condition;
            $repSelect = $this->query($select);
            if (empty($repSelect[0][0]['foreign_key'])) {
                $resultIds[] = 0;
            } else {
                foreach ($repSelect as $infosup)
                    if (!empty($infosup['0']['foreign_key']))
                        $resultIds[] = $infosup['0']['foreign_key'];
            }
        }

        return $resultIds;
    }

    function addField($champs, $id, $model = 'Deliberation') {
        $champs_def = $this->Infosupdef->read(null, $champs['infosupdef_id']);
        if ($champs_def['Infosupdef']['type'] == 'list' && $champs['text'] != '') {
            $tmp = $this->Infosupdef->Infosuplistedef->find('first', array('conditions' => array('Infosuplistedef.id' => $champs['text']),
                'fields' => array('Infosuplistedef.nom'),
                'recursive' => -1));
            $champs['text'] = $tmp['Infosuplistedef']['nom'];
        } elseif ($champs_def['Infosupdef']['type'] == 'list' && $champs['text'] == '')
            return (new GDO_FieldType($champs_def['Infosupdef']['code'], ' ', 'text'));

        if ($champs_def['Infosupdef']['type'] == 'listmulti' && $champs['text'] != '') {
            $tmp = $this->Infosupdef->Infosuplistedef->find('all', array(
                'conditions' => array('Infosuplistedef.id' => explode(',', str_replace("'", '', $champs['text']))),
                'fields' => array('Infosuplistedef.nom'),
                'recursive' => -1));
            $content = array();
            foreach ($tmp as $elt)
                $content[] = $elt['Infosuplistedef']['nom'];
            $champs['text'] = implode(', ', $content);
        } elseif ($champs_def['Infosupdef']['type'] == 'listmulti' && $champs['text'] == '')
            return (new GDO_FieldType($champs_def['Infosupdef']['code'], ' ', 'text'));
        if ($champs['text'] != null) {
            return (new GDO_FieldType($champs_def['Infosupdef']['code'], $champs['text'], 'lines'));
        } elseif ($champs['date'] != null) {
            include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
            $this->Date = new DateComponent;
            return (new GDO_FieldType($champs_def['Infosupdef']['code'], $this->Date->frDate($champs['date']), 'date'));
        } elseif ($champs['file_size'] != 0) {
            $name = str_replace(" ", "_", $champs['file_name']);
            return (new GDO_ContentType($champs_def['Infosupdef']['code'], $name, 'application/vnd.oasis.opendocument.text', 'binary', $champs['content']));
        } elseif ((!empty($champs['content'])) && ($champs['file_size'] == 0)) {
            include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/GedoooComponent.php');
            include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/ConversionComponent.php');
            $this->Gedooo = new GedoooComponent;
            $this->Conversion = new ConversionComponent;

            if ($model == 'Deliberation') {
                $filename = WEBROOT_PATH . "/files/generee/projet/$id/" . $champs_def['Infosupdef']['code'] . ".html";
                $this->Gedooo->createFile(WEBROOT_PATH . "/files/generee/projet/$id/", $champs_def['Infosupdef']['code'] . ".html", $champs['content']);
                $content = $this->Conversion->convertirFichier($filename, 'html', 'html', 'odt');
                $this->Gedooo->createFile(WEBROOT_PATH . "/files/generee/projet/$id/", $champs_def['Infosupdef']['code'] . ".odt", $content);
                return (new GDO_ContentType($champs_def['Infosupdef']['code'], $champs_def['Infosupdef']['code'] . ".odt", 'application/vnd.oasis.opendocument.text', 'binary', $content));
            } elseif ($model == 'Seance') {
                $filename = WEBROOT_PATH . "/files/generee/seance/$id/" . $champs_def['Infosupdef']['code'] . ".html";
                $this->Gedooo->createFile(WEBROOT_PATH . "/files/generee/seance/$id/", $champs_def['Infosupdef']['code'] . ".html", $champs['content']);
                $content = $this->Conversion->convertirFichier($filename, 'html', 'html', 'odt');
                return (new GDO_ContentType($champs_def['Infosupdef']['code'], $champs_def['Infosupdef']['code'] . ".odt", 'application/vnd.oasis.opendocument.text', 'binary', $content));
            }
        } elseif (empty($champs['text']))
            return NULL;
    }

    /**
     * Retourne les informations supplémentaire sous forme de tableau suivant leur type
     * @param $id
     * @param string $model
     * @return array
     */
    function export($id, $model = 'Deliberation') {
        $return = array();
        $infosups = $this->find('all', array(
            'fields' => array('Infosup.id', 'Infosupdef.type', 'Infosupdef.code',
                'Infosup.text', 'Infosup.date', 'Infosup.content',
                'Infosup.file_name', 'Infosup.file_type', 'Infosup.file_size',
            ),
            'conditions' => array('Infosup.foreign_key' => $id,
                'Infosup.model' => $model),
            'recursive' => 0));
        foreach ($infosups as $infosup) {
            if ($infosup['Infosupdef']['type'] == 'text') {
                $return[$infosup['Infosupdef']['code']] = array('type' => 'string',
                    'content' => $infosup['Infosup']['text']);
            } elseif ($infosup['Infosupdef']['type'] == 'richText') {
                $return[$infosup['Infosupdef']['code']] = array('type' => 'string',
                    'content' => $infosup['Infosup']['content']);
            } elseif ($infosup['Infosupdef']['type'] == 'date') {
                include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/DateComponent.php');
                $this->Date = new DateComponent;
                $return[$infosup['Infosupdef']['code']] = array('type' => 'string',
                    'content' => $this->Date->frDate($infosup['Infosup']['date']));
            } elseif ($infosup['Infosupdef']['type'] == 'file' || $infosup['Infosupdef']['type'] == 'odtFile') {
                $return[$infosup['Infosupdef']['code']] = array('type' => 'file',
                    'id' => $infosup['Infosup']['id'],
                    'file_name' => $infosup['Infosup']['file_name'],
                    'file_type' => $infosup['Infosup']['file_type'],
                    'content' => $infosup['Infosup']['content']);
            } elseif ($infosup['Infosupdef']['type'] == 'boolean') {
                $return[$infosup['Infosupdef']['code']] = array(
                    'type' => 'string',
                    'content' => $infosup['Infosup']['text']);
            } elseif ($infosup['Infosupdef']['type'] == 'list') {
                $ele = $this->Infosupdef->Infosuplistedef->find('first', array('fields' => array('nom'),
                    'conditions' => array('id' => $infosup['Infosup']['text']),
                    'recursive' => -1));
                if (!empty($ele['Infosuplistedef']['nom']))
                    $return[$infosup['Infosupdef']['code']] = array(
                        'type' => 'string',
                        'content' => $ele['Infosuplistedef']['nom']);
            } elseif ($infosup['Infosupdef']['type'] == 'listmulti') {
                $elts = $this->Infosupdef->Infosuplistedef->find('all', array(
                    'conditions' => array('id' => explode(',', str_replace("'", '', $infosup['Infosup']['text']))),
                    'fields' => array('nom'),
                    'recursive' => -1
                ));
                $content = array();
                foreach ($elts as $elt) {
                    $content[] = $elt['Infosuplistedef']['nom'];
                }
                $return[$infosup['Infosupdef']['code']] = array(
                    'type' => 'string',
                    'content' => implode(', ', $content)
                );
            }
        }
        return $return;
    }

    /**
     * fonction d'initialisation des variables de fusion pour l'allias utilisé pour la liaison (Rapporteur, President, ...)
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param string $modelName nom du modele lié
     * @param integer $id id du modèle lié
     */
    function setVariablesFusion(&$oMainPart, &$modelOdtInfos, $modelName, $id) {
        // lecture de la définition des infosup
        $allInfoSupDefs = $this->Infosupdef->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'code', 'type'),
            'conditions' => array('model' => $modelName)));

        // infosups utilisées dans le modèle d'édition
        $infoSupDefs = $infoSupDefIds = array();
        foreach ($allInfoSupDefs as $infoSupDef)
            if ($modelOdtInfos->hasUserFieldDeclared($infoSupDef['Infosupdef']['code'])) {
                $infoSupDefIds[] = $infoSupDef['Infosupdef']['id'];
                $infoSupDefs[$infoSupDef['Infosupdef']['id']] = $infoSupDef['Infosupdef'];
            }
        if (empty($infoSupDefIds))
            return;

        // lecture des valeurs des infosups
        $infosups = $this->find('all', array(
            'recursive' => -1,
            'fields' => array('infosupdef_id', 'text', 'date', 'content'),
            'conditions' => array(
                'model' => $modelName,
                'foreign_key' => $id,
                'infosupdef_id' => $infoSupDefIds)));

        // fusion des variables
        foreach ($infosups as $infosup) {
            switch ($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['type']) {
                case 'text':
                case 'boolean':
                    if (empty($infosup['Infosup']['text'])) break;
                    $oMainPart->addElement(new GDO_FieldType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], $infosup['Infosup']['text'], 'text'));
                    break;
                case 'date':
                    if (empty($infosup['Infosup']['date'])) break;
                    $oMainPart->addElement(new GDO_FieldType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], date('d/m/Y', strtotime($infosup['Infosup']['date'])), 'text'));
                    break;
                case 'list':
                    if (empty($infosup['Infosup']['text'])) break;
                    $listValue = $this->Infosupdef->Infosuplistedef->field('nom', array('id' => $infosup['Infosup']['text']));
                    $oMainPart->addElement(new GDO_FieldType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], $listValue, 'text'));
                    break;
                case 'listmulti':
                    if (empty($infosup['Infosup']['text'])) break;
                    $listValues = $this->Infosupdef->Infosuplistedef->nfield('nom', array('id' => explode(',', str_replace('\'', '', $infosup['Infosup']['text']))), array('ordre'));
                    $oMainPart->addElement(new GDO_FieldType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], implode(', ', $listValues), 'text'));
                    break;
                case 'richText':
                    $name = str_replace(" ", "_", $infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code']);
                    if (empty($infosup['Infosup']['content'])) 
                         $oMainPart->addElement(new GDO_ContentType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], $name, 'application/vnd.oasis.opendocument.text', 'binary', file_get_contents(APP.DS.'Config'.DS.'OdtVide.odt')));
                        break;
                    include_once(ROOT . DS . APP_DIR . DS . 'Controller/Component/ConversionComponent.php');
                    $this->Conversion = new ConversionComponent(new ComponentCollection());
                    $content = $this->Conversion->convertirFlux($infosup['Infosup']['content'], 'html', 'odt');
                    $oMainPart->addElement(new GDO_ContentType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], $name, 'application/vnd.oasis.opendocument.text', 'binary', $content));
                    break;
                case 'odtFile':
                    $name = str_replace(" ", "_", $infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code']);
                    if (empty($infosup['Infosup']['content']))
                        $oMainPart->addElement(new GDO_ContentType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], $name, 'application/vnd.oasis.opendocument.text', 'binary', file_get_contents(APP.DS.'Config'.DS.'OdtVide.odt')));
                        break;
                    
                    $oMainPart->addElement(new GDO_ContentType($infoSupDefs[$infosup['Infosup']['infosupdef_id']]['code'], $name, 'application/vnd.oasis.opendocument.text', 'binary', $infosup['Infosup']['content']));
                    break;
            }
        }
    }
}
