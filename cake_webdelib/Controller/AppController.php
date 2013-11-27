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
App::uses('Controller', 'Controller');
class AppController extends Controller {
        public $theme = "Bootstrap";
	public $components = array( 'Utils', 'Acl', 'Droits', 'Session');
	//var $components = array( 'Utils', 'Acl', 'Droits', 'Session', 'DebugKit.Toolbar');
	public $helpers = array('Html', 'Form', 'Js', 'Session', 'Menu', 'DatePicker', 'Html2' );
        public $aucunDroit = array('Pages:format', 'Pages:service');

	function beforeFilter() {
		$this->set('Droits',$this->Droits);
		$this->set('name',$this->name);
		$this->set('Droit', $this->Droits);
		$this->set('infoUser',$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom'));
		$this->set('user_id',$this->Session->read('user.User.id'));
		$this->set('agentServices',$this->Session->read('user.Service'));
		$this->set('lienDeconnexion', true);
		$this->set('session_service_id', $this->Session->read('user.User.service'));
		$this->set('session_menuPrincipal', $this->Session->read('menuPrincipal'));
                
                // ????
		if (CRON_DISPATCHER) return true;
                // Exception pour le bon déroulement de cron
                $action_accepted = array('runCrons', 'majTraitementsParapheur', 'traiterDelegationsPassees', 'generer');
                
                if (in_array($this->action, $action_accepted)) return true;
                
		if((substr($_SERVER['REQUEST_URI'], strlen($this->base)) != '/users/login') &&
				($this->action!='writeSession') &&
				(substr(substr($_SERVER['REQUEST_URI'], strlen($this->base)), 0,  strlen('/cakeflow/traitements/traiter_mail')) != '/cakeflow/traitements/traiter_mail'))
		{

			//s'il n'y a pas d'utilisateur connecte en session
			if (!$this->Session->Check('user')) {
				$this->redirect("/users/login");
			}
			else {
  
				// Contrôle des droits
                             $Pages = array('Pages:format', 'Pages:service');
			     $controllerAction = $this->name . ':' . ($this->name == 'Pages' ? $this->params['pass'][0] : $this->action);
                             if (in_array($controllerAction, $Pages)) {
                                 return true;
                             }
                             elseif  ($controllerAction != 'Deliberations:delete') {  
			         $user_id = $this->Session->read('user.User.id');
				 if (!$this->Droits->check($user_id, $controllerAction)) {
				     $this->Session->setFlash("Vous n'avez pas les droits nécessaires pour accéder à : $controllerAction", 'growl', array('type'=>'erreur'));
				     $this->redirect('/');
				 }
                                 else
                                     $this->log("$user_id => $controllerAction", 'trace');
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
			$this->Session->write( 'user.User.myUrl', $this->here );

			// Attention au cas de elements
			if (($this->here != $this->referer())                &&
					($this->here != '/deliberations/classification') &&
					($this->here != '/seances/voter') &&
					($this->here != '/deliberations/listerPresences')) {
                                $pos =  strpos(Router::url( null, true ), 'Ajax');
                                if ($pos === false) {
                                    $protocol = "http://";
                                    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
                                            !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 )
                                        $protocol = "https://";
                                    
                                    if(substr( $this->referer(), 0, 4) != 'http' )
                                        $url = $protocol. $this->referer();
                                    else
                                        $url =  $this->referer();

                                    $this->Session->write( 'user.User.lasturl', $url );
                                }

                       }
		}
	}

	function beforeRender() {
            if( $this->Session->check( 'user.User' ) ) {
                $pos =  strpos(Router::url( null, true ), 'Ajax');
                if ($pos === false) {
                    $this->Session->write( 'user.User.oldurl', Router::url( null, true ) );
                }
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

        /**
         * @see: http://stackoverflow.com/a/1459794/59087
         * @see: http://shiflett.org/blog/2006/mar/server-name-versus-http-host
         * @see: http://stackoverflow.com/a/3290474/59087
         * 
         * @param type $cookieName
         * @param type $cookieValue
         * @param type $httpOnly
         * @param type $secure
         */
    public function setCookieToken($cookieName, $cookieValue, $httpOnly = true, $secure = false) {
        setcookie(
                $cookieName, $cookieValue, 2147483647, // expires January 1, 2038
                "/", // your path
                $_SERVER["HTTP_HOST"], // your domain
                $secure, // Use true over HTTPS
                $httpOnly // Set true for $AUTH_COOKIE_NAME
        );
    }
}
