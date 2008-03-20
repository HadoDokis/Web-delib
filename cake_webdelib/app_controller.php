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

	var $components=array( 'Utils', 'Acl', 'Droits');
	var $helpers = array('Menu', 'Html', 'Ajax', 'Form', 'Javascript');

	var $beforeFilter = array('checkSession');

	var $infoUser = "";
	var $lienDeconnexion = "";
	var $agentServices = null;
	var $userProfil = null;

	function checkSession() {
		$this->infoUser = "<span class=\"user\">".$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom')."</span> ";
   		$this->agentServices = $this->Session->read('user.Service');
 	    $this->lienDeconnexion = "[<span class=\"deconnexion\"><a href=\"".$this->base."/users/logout\"> Deconnexion</a></span>]";

		if(substr($_SERVER['REQUEST_URI'], strlen($this->base)) != '/users/login')
		{
			//s'il n'y a pas d'utilisateur connecte en session
			if (!$this->Session->Check('user')) {
				//le forcer a se connecter
				$this->redirect("/users/login");
				exit();
			}
			else {
		 		// Contrôle des droits
		 		$controllerAction = $this->name . ':' . ($this->name == 'Pages' ? $this->params['pass'][0] : $this->action);
				$user_id = $this->Session->read('user.User.id');
				if ($this->Droits->check($user_id, $controllerAction)) {
        			if ($controllerAction != 'Services:doList') {
        		   	    $this->log($_SERVER["REMOTE_ADDR"]." : ($user_id)->".substr($this->here, 0, strlen($this->here)));
        			}
          		    return;
        		}
                else {
                   if (DEBUG==1)
                       die("accès refusé pour $user_id (".$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom').") à $controllerAction");
                    else
                        $this->redirect('/users/logout');

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

}
?>