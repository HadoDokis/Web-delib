<?php
/* SVN FILE: $Id: app_model.php 4409 2007-02-02 13:20:59Z phpnut $ */
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
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4409 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:20:59 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppModel extends Model{

 /*
 * Equivalent du find('list') mais sur plusieurs champs du model
 * $params permet de passer tous les paramètres à la fonction sous la forme :
 * array(
 *		'fields' => array('field1', 'field2'), //array of field names
 *		'format' => string '%s, %s ....', (format utilisé dans la fonction php printf)
 *		'conditions' => array('field' => $thisValue), //array of conditions
 *		'order' => array('Model.field1', 'Model.field2 DESC') //string or array defining order
 *		);
 * Les clés manquantes sont initialisées par la variable du modèle $displayFields
 * Si 'fields' ou 'format' sont vides ou ne sont pas définis, alors la fonction retourne find('list')
 */
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
	foreach($recs as $rec) {
		$id = $rec[$this->alias][$this->primaryKey];
		if ($clePrimaireAjoutee) unset($rec[$this->alias][$this->primaryKey]);
		$ret[$id] = vsprintf($params['format'], $rec[$this->alias]);
	}

	return $ret;
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
}
?>
