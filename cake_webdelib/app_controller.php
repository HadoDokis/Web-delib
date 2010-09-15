<?php
/* SVN FILE: $Id: app_controller.php 4409 2007-02-02 13:20:59Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppController extends Controller {

	var $components = array( 'Utils', 'Acl', 'Xacl', 'Droits');
	var $helpers = array('Html', 'Form', 'Javascript', 'Session', 'Menu', 'DatePicker');
	//var $beforeFilter = array('requireLogin'=>array('except'=>array('index')));

	function beforeFilter() {
		$this->set('Droits',$this->Droits);
		$this->set('name',$this->name);
		$this->set('Xacl',$this->Xacl);
		$this->set('infoUser',"<span class=\"user\">".$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom')."</span> ");
		$this->set('user_id',$this->Session->read('user.User.id'));
		$this->set('agentServices',$this->Session->read('user.Service'));
		$this->set('lienDeconnexion',"[<span class=\"deconnexion\"><a href=\"".$this->base."/users/logout\"> D&eacute;connexion</a></span>]");
		$this->set('session_service_id', $this->Session->read('user.User.service'));
		$this->set('session_menuPrincipal', $this->Session->read('menuPrincipal'));

        if (CRON_DISPATCHER) return true;

		if((substr($_SERVER['REQUEST_URI'], strlen($this->base)) != '/users/login')&&($this->action!='writeSession'))
		{

			//s'il n'y a pas d'utilisateur connecte en session
			if (!$this->Session->Check('user')) {
				//le forcer a se connecter
				$this->redirect("/users/login");
				//exit();
			}
			else {
		 		// Contrôle des droits
		 		$controllerAction = $this->name . ':' . ($this->name == 'Pages' ? $this->params['pass'][0] : $this->action);
				$user_id = $this->Session->read('user.User.id');
				if ($this->Droits->check($user_id, $controllerAction)) {
        			    if ($controllerAction != 'Services:doList') {
        		   	        $this->log($_SERVER["REMOTE_ADDR"]." : ($user_id)->".substr($this->here, 0, strlen($this->here)));
        			    }
        		        }
                                else {
                                    $this->log("Tentative d'accès $user_id à $controllerAction");
                                    $this->Session->setFlash("Vous n'avez pas les droits nécessaires pour accéder à : $controllerAction", 'growl', array('type'=>'erreur'));
                                    $this->redirect('/');
                               }
                           }
                 }
	}

	function externLogin($login = null, $password = null) {
		$user = $this->User->findByLogin($login);

		//si le mdp n'est pas vide et correspond a celui de la bdd
		if (!empty($password) && ($user['User']['password'] == md5($password)))
		{
			//on stocke l'utilisateur en session
			$this->Session->write('user',$user);
			//services auquels appartient l'agent
			if(empty ($user['Service'])){
				$this->Session->write('user.User.service', $user['ServiceElu']['id']);
			}else{
    			$services = $this->Utils->simplifyArray($user['Service']);
    			foreach ($services as $key=>$service){
    				$service = $this->requestAction("services/doList/$key");
    				$services[$key]=$service;
    			}
    			$this->Session->write('user.Service',$services);
    			$this->Session->write('user.User.service', key($services));
				}
				$this->redirect('/');
 		}
	}

	function afterFilter() {
		if( $this->Session->check( 'user.User' ) ) {
                   // Attention au cas de elements $this->log( $this->Session->read( 'user.User.oldurl' )); 
                    if (($this->here != $this->referer()) && ($this->here != '/deliberations/classification'))
		        $this->Session->write( 'user.User.lasturl', $this->referer() );
		}
	}

	function beforeRender() {
		if( $this->Session->check( 'user.User' ) ) {
			$this->Session->write( 'user.User.oldurl', Router::url( null, true ) );
		}
	}

	function _selectedArray($data, $key = 'id') {
		if (!is_array($data)) {
			$model = $data;
			if (!empty($this->data[$model][$model])) {
				return $this->data[$model][$model];
			}
			if (!empty($this->data[$model])) {
				$data = $this->data[$model];
			}
		}
		$array = array();
		if (!empty($data)) {
			foreach ($data as $var) {
				$array[$var[$key]] = $var[$key];
			}
		}
		return $array;
	}

}
?>
