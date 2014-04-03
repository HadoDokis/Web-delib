<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *                                1785 E. Sahara Avenue, Suite 490-204
 *                                Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright        Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link                http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package            cake
 * @subpackage        cake.cake
 * @since            CakePHP(tm) v 0.2.9
 * @version            $Revision: 4409 $
 * @modifiedby        $LastChangedBy: phpnut $
 * @lastmodified    $Date: 2007-02-02 07:20:59 -0600 (Fri, 02 Feb 2007) $
 * @license            http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package        cake
 * @subpackage    cake.cake
 */
class AppModel extends Model {
    //var $actsAs=array('Containable');

    /**
     * Validation du format de fichier par FIDO
     * @param string|array $data flux d'un fichier ou tableau de type HTTP Post
     * @param null|string|array $extension extension(s) autorisée(s), si null autorise toutes celles du fichier formats.inc
     * @param bool $required autoriser qu'il n'y ai pas de fichier
     * @return bool fichier autorisé ou non
     */
    public function checkFormat($data, $extension = null, $required = false) {
        App::uses('FidoComponent', 'ModelOdtValidator.Controller/Component');
        $this->Fido = new FidoComponent();
        if (is_array($data)) {
            $data = array_shift($data);
            if (!$required && $data['error'] == 4) {
                return true;
            }
            if ($required && $data['error'] != 0) {
                return false;
            }
            if ($data['size'] == 0 || $data['error'] != 0) {
                $this->validate['content']['message'] = 'Erreur dans le document ou lors de l&apos;envoi.';
                return false;
            }
            $allowed = $this->Fido->checkFile($data['tmp_name']);
        } else {
            if (empty($data))
                return !$required;
            $file = new File(tempnam(TMP, 'upload_'));
            $file->write($data);
            $allowed = $this->Fido->checkFile($file->path);
            $file->delete();
        }
        if (is_null($extension))
            return $allowed;
        elseif (is_array($extension)){
            $result=false;
            foreach($extension as $ext){
                if(!$result && !empty($this->Fido->lastResults['extension']))
                $result=in_array($ext, is_array($this->Fido->lastResults['extension'])?$this->Fido->lastResults['extension']:array($this->Fido->lastResults['extension']));
            }
            return $allowed && $result;
        }
        elseif (is_string($extension))
            return $allowed && $this->Fido->lastResults['extension'] == $extension;
        else
            return false;
    }

    function listFields($params = array()) {
        // Initialisation des clés manquantes de $params avec les valeurs de $this->$displayFields
        if (isset($this->displayFields))
            $params = array_merge($this->displayFields, $params);

        // Si la liste des champs ou le format ne sont pas définis on retourne la fonction find('list')
        if (empty($params['fields']) || empty($params['format']))
            return $this->find('list', $params);

        // Ajout de la clé primaire dans la liste des champs si elle n'y est pas déjà
        $clePrimaireAjoutee = false;
        if (!in_array($this->primaryKey, $params['fields'])) {
            $params['fields'][] = $this->primaryKey;
            $clePrimaireAjoutee = true;
        }

        // On force la récursivite à -1
        $params['recursive'] = -1;

        // Execution du find
        $recs = $this->find('all', $params);

        // Constitution de la liste de retour
        $ret = array();
        foreach ($recs as $rec) {
            $id = $rec[$this->alias][$this->primaryKey];
            if ($clePrimaireAjoutee) unset($rec[$this->alias][$this->primaryKey]);
            $ret[$id] = vsprintf($params['format'], $rec[$this->alias]);
        }

        return $ret;
    }

    function changeBoolean($model, $id, $field) {
        $mod = new $model;
        $data = $mod->find('first', array('conditions' => array("$model.id" => $id),
            'recursive' => -1,
            'fields' => array("$field")));
        $mod->id = $id;
        return ($mod->saveField($field, !$data[$model][$field]));
    }

    /*function isUnique($field, $value, $id)
        {
            $fields[$this->name.'.'.$field] = $value;
            if (empty($id))
                // add
                $fields[$this->name.'.id'] = "!= NULL";
            else
                // edit
                $fields[$this->name.'.id'] = "!= $id";

            $this->recursive = -1;
            if ($this->hasAny($fields))
            {
                $this->invalidate('unique_'.$field);
                return false;
            }
            else
                return true;
       }*/

    public function isUploadedFile($params) {
        $val = array_shift($params);
        if ((isset($val['error']) && $val['error'] == 0) ||
            (!empty($val['tmp_name']) && $val['tmp_name'] != 'none')
        ) {
            return true;
        }
        return false;
    }

    /**
     * équivalent de la fonction cake field() mais retourne plusieurs valeurs sous forme de tableau
     */
    function nfield($fieldName, $conditions = array(), $order = array()) {
        // initialisations
        $ret = array();
        $fields = array();
        $contain = array();

        // champ a lire
        $fields[] = $fieldName . ' DISTINCT';

        // ajout des champs des modeles liées pour la condition
        foreach ($conditions as $condField => &$cond) {
            if (strpos($condField, ' ') !== false)
                $condField = substr($condField, 0, strpos($condField, ' '));
            if (strpos($condField, '.') !== false) {
                $tabCondField = explode('.', $condField);
                $condModel = $tabCondField[0];
                if ($condModel != $this->alias) $contain[] = $condField;
            }
        }
        // ajout des champs des modeles liées pour l'ordre
        foreach ($order as $orderField) {
            if (strpos($orderField, ' ') !== false)
                $orderField = substr($orderField, 0, strpos($orderField, ' '));
            if (strpos($orderField, '.') !== false) {
                $tabOrderField = explode('.', $orderField);
                $orderModel = $tabOrderField[0];
                if ($orderModel != $this->alias)
                    $contain[] = $orderField;
                else
                    $fields[] = $orderField;
            } else
                $fields[] = $orderField;
        }

        // lecture en base
        $this->Behaviors->load('Containable');
        $occurs = $this->find('all', array(
            'fields' => $fields,
            'contain' => $contain,
            'conditions' => $conditions,
            'order' => $order));

        // constitution de la liste
        foreach ($occurs as $occur)
            $ret[] = $occur[$this->alias][$fieldName];
        return $ret;
    }

}

?>
